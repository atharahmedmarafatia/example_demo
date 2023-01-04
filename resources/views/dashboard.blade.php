<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Company Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <a href="{{route('company.create')}}" class="btn btn-primary">Create new company</a>
                    @if(session()->has('message'))
                        <div class="alert alert-success" x-data="{show:true}" x-init="setTimeout(() => show = false,4000)" x-show="show">
                            <p>{{ session()->get('message') }}</p>
                        </div>
                    @endif
                    <table class="table table-bordered company_datatable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>User</th>
                                <th>Country Name</th>
                                <th>Date</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@include('modal.comapny_usersmodal');
<script>
    $(function() {
        var tbl = $(".company_datatable").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('company')}}",
            columns: [
                {data: 'name', name: 'name'},
                {data: 'user_id', name: 'user_id'},
                {data: 'country_id', name: 'country_id'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action'},
            ]
        });
    });

    
$(document).on("click", "#show", function(e){
    e.preventDefault();
    var id = $(this).attr('data-id');
    $.ajax({
        type:"GET",
        url:  "{{route('company.getUserData')}}",
        token: "{{ csrf_token() }}",
        data: {id:id},
        dataType: 'json',
        success: function(res){
            $('#tbluserinfo tbody').html(res.html);
            $("#userModal").modal("show");
        }
    })
});
</script>
