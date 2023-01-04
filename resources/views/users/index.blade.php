<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Companies
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <a href="{{route('user.company.create')}}" class="btn btn-primary">Add new company</a>
                    @if(session()->has('message'))
                        <div class="alert alert-success" x-data="{show:true}" x-init="setTimeout(() => show = false,4000)" x-show="show">
                            <p>{{ session()->get('message') }}</p>
                        </div>
                    @endif
                    <table class="table table-bordered user_company_datatable">
                        <thead>
                            <tr>
                                <th>My Company Name</th>
                                <th>Date</th>
                                <th>Action</th>
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
        var tbl = $(".user_company_datatable").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('user.company.index')}}",
            columns: [
                {data: 'company', name: 'company'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action'},
            ]
        });
    });
</script>
