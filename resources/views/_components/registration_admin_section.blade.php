<fieldset class="transaction">
        <legend>Sponsor</legend>
    
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Sponsor:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                
                <select name="sponsor[sponsor_id]" class="form-control"
                        class="select2">
                    <option value=""> Select Sponsor if appropriate</option>
	                @foreach(\App\Sponsor::orderBy('name')->pluck('name','id') as $id=>$name)
		                <option value="{{$id}}"
		                        @if(old('sponsor_id') == $id or (isset($delegate) and optional($delegate->sponsorRecord)->sponsor_id == $id )) selected @endif>{{$name}}</option>
	                @endforeach
                </select>
	            @if ($errors->has('sponsor.sponsor_id'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('sponsor.sponsor_id') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Correspondent Name:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                <input class="form-control"
                       name="sponsor[name]"
                       value="{{old('sponsor[name]')??(isset($delegate) ? optional($delegate->sponsorRecord)->name: null)}}">
	            @if ($errors->has('sponsor.name'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('sponsor.name') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Correspondent Email:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                <input class="form-control"
                       type="email"
                       name="sponsor[email]"
                       value="{{old('sponsor[email]')??(isset($delegate) ? optional($delegate->sponsorRecord)->email: null)}}">
	            @if ($errors->has('sponsor.email'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('sponsor.email') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Correspondent Tel:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                 <input class="form-control"
                        type="tel"
                        name="sponsor[tel]"
                        value="{{old('sponsor[tel]')??(isset($delegate) ? optional($delegate->sponsorRecord)->tel: null)}}">
	            @if ($errors->has('sponsor.tel'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('sponsor.tel') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Correspondent Address:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                <input class="form-control"
                       type="address"
                       name="sponsor[address]"
                       value="{{old('sponsor[address]')??(isset($delegate) ? optional($delegate->sponsorRecord)->address: null)}}">
	            @if ($errors->has('sponsor[address]'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('sponsor[address]') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
</fieldset>
<fieldset class="transaction">
        <legend>Delegate Role</legend>
    
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Role:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                {{Form::select('roles_id[]',\App\DelegateRole::pluck('label','id'), null, ['class'=>'form-control', 'multiple'])}}
	            @if ($errors->has('roles_id[]'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('roles_id[]') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
</fieldset>
<fieldset class="transaction">
        <legend>Transaction Status</legend>
    
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Transaction Status:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                {{Form::select('status',$status, null, ['class'=>'form-control'])}}
	            @if ($errors->has('status'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('status') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-6 col-md-4 col-lg-3 col-form-label">Note:</label>
            <div class="col-sm-6 col-md-8  col-lg-9">
                {{Form::textarea('note', null, ['class'=>'form-control'])}}
	            @if ($errors->has('note'))
		            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('note') }}</strong>
                    </span>
	            @endif
            </div>
        </div>
</fieldset>

