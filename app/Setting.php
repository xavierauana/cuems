<?php

namespace App;

use App\Listener\InvalidateSettingCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];
    protected $appends = [
        'urls',
    ];

    private $sortableColumns = ['key'];
    private $searchableColumns = ['key'];

    protected $dispatchesEvents = [
        'saved'   => InvalidateSettingCache::class,
        'deleted' => InvalidateSettingCache::class,
    ];

    public function getUrlsAttribute() {
        return [
            'edit'   => route('events.settings.edit',
                [$this->event_id, $this->id]),
            'delete' => route('events.settings.destroy',
                [$this->event_id, $this->id]),
        ];
    }

    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function getCacheKey(): string {
        return "settings_{$this->event_id}_{$this->key}";
    }

    public function invalidateCache(): void {
        start_measure('invalidate', 'Time for invalidating');

        \Debugbar::info('going to invalidate cache');
        \Debugbar::info(cache()->has($this->getCacheKey()));
        cache()->forget($this->getCacheKey());
        \Debugbar::info(cache()->has($this->getCacheKey()));
        stop_measure('invalidate');
    }

    /**
     * @return array
     */
    public function getSortableColumns(): array {
        return $this->sortableColumns;
    }

    /**
     * @return array
     */
    public function getSearchableColumns(): array {
        return $this->searchableColumns;
    }

}
