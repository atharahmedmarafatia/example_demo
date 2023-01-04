<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload File') }}
        </h2>
    </x-slot>
    {{-- ex using component x-component_name --}}
    @if(session()->has('message'))
        <div class="alert alert-success" x-data="{show:true}" x-init="setTimeout(() => show = false,4000)" x-show="show">
            <p>{{ session()->get('message') }}</p>
        </div>
    @endif

    <form action="{{ route('file.store') }}" enctype="multipart/form-data"
    method="POST">
        @csrf
        <div class="main-form">
            <div class="form-group">
                <div class="form-floating">
                    <input type="file" class="h-full w-full opacity-0" name="file" >
                    <label for="file">File upload Click here</label>
                    @error('file')
                    <span class="block text-gray-400 font-normal">Attach
                        you files here</span> <span
                        class="block text-gray-400 font-normal">or</span>
                    <span class="block text-blue-400 font-normal">Browse
                        files</span>
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



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session()->has('message'))
                        <div class="alert alert-success" x-data="{show:true}" x-init="setTimeout(() => show = false,4000)" x-show="show">
                            <p>{{ session()->get('message') }}</p>
                        </div>
                    @endif
                    <table class="table table-bordered file_datatable">
                        <thead>
                            <tr>
                                <th>Original filename</th>
                                <th>Content</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    
</x-app-layout>

<script>
    $(function() {
        var tbl = $(".file_datatable").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('file')}}",
            columns: [
                {data: 'orig_filename', name: 'orig_filename'},
                {data:'content',name:'content'},
                {data: 'created_at', name: 'created_at'},
            ]
        });
    });
</script>