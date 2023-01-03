<x-app-layout>
    <form action="{{route('company.update',$company->id)}}" method="POST" class="step-1 creatives-form">
        @csrf
        @method('put')
        <div class="main-form">
            <div class="form-group">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" id="name" placeholder="marfatia" maxlength="100" value="{{ $company->name }}">
                    <label for="name">Company name</label>
                    @error('name')
                    <span style="display: inline-block;" class="alert alert-danger" role="alert">{{ $message }}
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="form-floating">
                    <select class="form-select" name="country_id" id="country_id">
                        <option selected>Open this select menu</option>
                        @foreach ($country as $key => $countries)
                            <option value="{{$countries->id }}" {{($countries->id == $company->country_id) ? 'selected' : ''}}>{{$countries->name}}</option>
                        @endforeach
                    </select>
                    <label for="country_id">Country Name</label>
                    @error('country_id')
                    <span style="display: inline-block;" class="alert alert-danger" role="alert">{{ $message }}
                    </span>
                    @enderror
                </div>
            </div>          

            <div class="form-group">
                <div class="form-floating">
                    <select class="form-select js-example-basic-multiple" name="user_ids[]" multiple="multiple" id="user_ids" >

                        @php
                            if($company->users->count() > 0){
                                $selecteduser_id=$company->users->pluck('id')->toArray();
                            }else{
                                $selecteduser_id=[];
                            }
                        @endphp
                        @foreach($users as $user)
                            <option value="{{$user->id}}" {{ (!empty($selecteduser_id) && in_array($user->id,$selecteduser_id)) ? 'selected' : '' }} >{{$user->name}}</option>
                        @endforeach
                    </select>
                    <label for="user_ids">User name</label>
                    @error('user_ids')
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