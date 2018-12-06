Hi {{$delegate->name}},
Refund invoice {{optional($delegate->transactions->first())->charge_id ?? "NA"}}