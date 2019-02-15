<?php

namespace Tests\Unit;

use App\Delegate;
use App\DelegateRole;
use App\Enums\PaymentRecordStatus;
use App\Enums\TransactionStatus;
use App\Event;
use App\PaymentRecord;
use App\Services\DelegateCreationService;
use App\Services\JETCOPaymentService;
use App\Ticket;
use App\TransactionType;
use App\User;
use Faker\Generator;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;


/**
 * Class DelegateCreationServiceTest
 * @package Tests\Unit
 */
class DelegateCreationServiceTest extends TestCase
{

    use DatabaseMigrations, InteractsWithSession;

    /**
     * @var DelegateCreationService
     */
    private $service;

    private $faker;

    private $event;

    private $user;

    private $ticket;

    /**
     * @ Collection var
     */
    private $transactionTypes;

    /**
     *
     */
    protected function setUp() {

        parent::setUp();

        $this->faker = app(Generator::class);

        $this->user = factory(User::class)->create();

        $this->event = factory(Event::class)->create();

        factory(DelegateRole::class)->create([
            'is_default' => true
        ]);

        $this->ticket = factory(Ticket::class)->create([
            'event_id' => $this->event->id
        ]);
        $this->actingAs($this->user);

        $this->service = app(DelegateCreationService::class,
            ['user' => $this->user]);

        $types = [
            'Credit Card',
            'Cheque',
            'Bank In'
        ];

        $createdTypes = [];

        foreach ($types as $type) {
            $createdTypes[] = factory(TransactionType::class)->create([
                'label' => $type
            ]);
        }

        $this->transactionTypes = collect($createdTypes);
    }

    /**
     * @test
     */
    public function admin_create_delegate() {

        $transactionType = $this->transactionTypes->shuffle()->first();

        $data = $this->createDelegateData();

        $data['transaction_type_id'] = $transactionType->id;

        $newDelegate = $this->service->adminCreate($this->event, $data);

        $this->assertDatabaseHas('delegates', [
            'event_id'        => $this->event->id,
            'email'           => $data['email'],
            'id'              => $newDelegate->id,
            'registration_id' => 1,
        ]);

        $this->assertEquals($transactionType->id,
            $newDelegate->transactions()->first()->transactionType->id);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function delegate_self_registration() {

        $amount = 100;
        $invoiceNumber = 'test_invoice_number';

        $data = $this->createDelegateData();

        $record = PaymentRecord::updateOrCreate([
            'invoice_id' => $invoiceNumber,
            'event_id'   => $this->event->id
        ], [
            'status'    => PaymentRecordStatus::CREATED,
            'form_data' => json_encode($data),
        ]);

        $this->assertEquals(PaymentRecordStatus::CREATED, $record->status);

        $response = (new JETCOPaymentService)->charge($invoiceNumber, $amount);

        $newDelegate = $this->service->selfCreate($this->event, $data,
            $response, $record);

        $this->assertDatabaseHas('delegates', [
            'event_id'        => $this->event->id,
            'email'           => $data['email'],
            'id'              => $newDelegate->id,
            'registration_id' => 1,
        ]);
        // Transaction
        $this->assertDatabaseHas('transactions', [
            'payee_type'          => Delegate::class,
            'payee_id'            => $newDelegate->id,
            'transaction_type_id' => TransactionType::whereLabel('Credit Card')
                                                    ->firstOrFail()->id,
        ]);

        $record->refresh();

        $this->assertEquals(PaymentRecordStatus::AUTHORIZED, $record->status);

        $this->assertEquals($this->ticket->id,
            $newDelegate->transactions()->first()->ticket->id);
        $this->assertEquals($invoiceNumber,
            $newDelegate->transactions()->first()->charge_id);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function create_2_delegates_with_incremental_registration_id() {

        $data1 = $this->createDelegateData();
        $data2 = $this->createDelegateData();

        $newDelegate1 = $this->service->adminCreate($this->event, $data1);
        $newDelegate2 = $this->service->adminCreate($this->event, $data2);

        $this->assertDatabaseHas('delegates', [
            'event_id'        => $this->event->id,
            'email'           => $data1['email'],
            'id'              => $newDelegate1->id,
            'registration_id' => 1,
        ]);
        $this->assertDatabaseHas('delegates', [
            'event_id'        => $this->event->id,
            'email'           => $data2['email'],
            'id'              => $newDelegate2->id,
            'registration_id' => $newDelegate1->registration_id + 1,
        ]);
    }

    /**
     * @return array
     */
    private function createDelegateData(): array {
        $data = [
            'prefix'      => $this->faker->title,
            'first_name'  => $this->faker->firstName,
            'last_name'   => $this->faker->lastName,
            'is_male'     => 1,
            'email'       => $this->faker->companyEmail,
            'mobile'      => $this->faker->phoneNumber,
            'position'    => $this->faker->jobTitle,
            'department'  => "Department",
            'institution' => $this->faker->company,
            'address_1'   => $this->faker->address,
            'address_2'   => $this->faker->address,
            'address_3'   => $this->faker->address,
            'country'     => $this->faker->country,
            'ticket_id'   => $this->ticket->id,
            'status'      => TransactionStatus::COMPLETED
        ];

        return $data;
    }

}
