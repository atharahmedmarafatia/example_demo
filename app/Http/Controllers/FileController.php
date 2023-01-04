<?php

namespace App\Http\Controllers;
use Smalot\PdfParser\Parser;
use App\Models\File;
use Validator;
use DataTables;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->ajax()) {
            $data = File::get();
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('file',function($row) {
                    // return $row->users->first()->name;
                    $btn_user = '<a href="" data-id="'.$row->id.'" id="show" class="btn btn-primary btn-sm">show</a>';
                    return $btn_user;
                })
                ->addColumn('content',function($row) {
                    return nl2br($row->content);
                })
                ->addColumn('created_at',function($row) {
                    return $row->created_at->format('D M d, Y');
                })
                // ->addColumn('action', function($row){
                   
                //     $btn = '<a href="" data-id="'.$row->id.'" target="_blank" id="download" class="btn btn-warning btn-sm">Download</a>';
                //     return $btn;
                // })
                ->rawColumns(['file','content'])
                ->make(true);
        }
        return view('file');
    }

    public function downloadFile(Request $request)
    {
        // dd($request->all());
        $file_id = File::find($request->id);
        $pathToFile = public_path()."/". "pdf/".$file_id->orig_filename;
        $headers = ['Content-Type: application/pdf'];
        return response()->download($pathToFile,'test.pdf', $headers);
    }


    public function store(Request $request)
    {
        $file = $request->file;
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => "Fail",
                "success" => false,
                'code' => 415,
                'message' => $validator->errors(),
            ],415);
        }
        // use of pdf parser to read content from pdf 
        $fileName = $file->getClientOriginalName();

        $pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($file->path());
        $content = $pdf->getText();
        // dd($content);

        if(!str_contains(strtolower($content),"proposal")) {
            $msg = [
                "success" => false,
                'status' => "Fail",
                'code' => 422,
                'message' => "File not contain Proposal word"
            ];
            return response()->json($msg,422);
        }
    //    $upload_file = new File;
    //    $upload_file->orig_filename = $fileName;
    //    dd($file->getSize());
    //    $upload_file->mime_type = $file->getMimeType();
    //    $upload_file->filesize = $file->getSize();
    //    $upload_file->content = $content;
    //    $upload_file->save();

       $file_store = File::updateOrCreate(
            ['filesize' => $file->getSize(), 'orig_filename' => $fileName],
            ['mime_type' => $file->getMimeType(), 'content' => $content]
        );
        if($file_store)
        {
            $file->move(public_path('pdf'), $fileName);
        }
       return redirect()->back()->with('message', 'File uploaded successfully!');
    }


}
