<x-app-layout>
    <form action="{{route('user.company.store')}}" method="POST" class="step-1 creatives-form">
        @csrf
        <div class="main-form">
            <div class="form-group">
                <div class="form-floating">
                    <select class="form-select js-example-basic-multiple" name="company_id[]" multiple="multiple" id="company_id" >
                        
                        @php
                           if($users->company->count() > 0){
                                $selectedcompany_id=$users->company->pluck('id')->toArray();
                            }else{
                                $selectedcompany_id=[];
                            }
                        @endphp
                        @foreach($users as $user)
                            <option value="{{$user->id}}" {{ (!empty($selectedcompany_id) && in_array($user->id,$selectedcompany_id)) ? 'selected' : '' }} >{{$user->name}}</option>
                        @endforeach

                        
                        @foreach ($companies as $key => $company)
                            <option value="{{$company->id}}" {{ old('company_id') }}>{{$company->name}}</option>
                        @endforeach
                    </select>
                    <label for="company_id">Company name</label>
                    @error('company_id')
                    <span style="display: inline-block;" class="alert alert-danger" role="alert">{{ $message }}
                    </span>
                    @enderror
                </div>
            </div>
        </div>
       
        <div class="form-group full">
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
    </form>
</x-app-layout>
<script>
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
</script>