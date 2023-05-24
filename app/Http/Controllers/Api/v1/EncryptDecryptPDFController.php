<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\EncryptedPdfs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use mikehaertl\pdftk\Pdf;

class EncryptDecryptPDFController extends Controller
{
    public function encrypt_pdf(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf'
        ]);

        try {
            $pdfFile = $request->file('file');

            $password = Str::password();
            $name = $pdfFile->getClientOriginalName();
            $dotSeparatedName = explode('.', $name);
            $dashSeparatedName = explode('-', reset($dotSeparatedName));
            $hash = reset($dashSeparatedName);
            $response = $this->checkHashDuplication($hash);
            if(!empty($response)) {
                return response()->json($response, 422);
            }
            $pdfHash = encrypt($hash);

            $encryptedFilePath = storage_path('app/pdfs/'.$name);
            $pdf = new Pdf($pdfFile->path());
            $pdf->allow('AllFeatures')
            ->setUserPassword($password)
            ->passwordEncryption()
            ->saveAs($encryptedFilePath);

            EncryptedPdfs::create([
                "hash" => $pdfHash,
                "code" => encrypt($password),
                "created_at" => now()
            ]);

            return response()->download($encryptedFilePath)->deleteFileAfterSend(true);
        } catch(Exception $e) {
            return response()->json([
                'message' => 'Something goes wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function decrypt_pdf(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf'
        ]);

        try {
            $pdfFile = $request->file('file');
            $name = $pdfFile->getClientOriginalName();
            $dotSeparatedName = explode('.', $name);
            $dashSeparatedName = explode('-', reset($dotSeparatedName));
            $hash = reset($dashSeparatedName);
            $response = $this->findHash($hash);
            if(isset($response["error"])) {
                return response()->json($response, 422);
            }
            $unlockedFilePath = storage_path('app/pdfs/' . $name);
            $pdf = new Pdf;
            $pdf->addFile($pdfFile->path(), null, decrypt($response))->saveAs($unlockedFilePath);
            return response()->download($unlockedFilePath)->deleteFileAfterSend();
        } catch(Exception $e) {
            return response()->json([
                'message' => 'Something goes wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete_pdf(Request $request)
    {
        $request->validate([
            'hash' => 'required'
        ]);

        $hashes = EncryptedPdfs::pluck('hash', 'id')->toArray();
        foreach ($hashes as $id => $hash) {
            if(decrypt($hash) == $request->hash) {
                EncryptedPdfs::where('id', $id)->delete();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'PDF has been successfully deleted from the system.'
        ], 200);
    }

    private function checkHashDuplication($checkHash)
    {
        $hashes = EncryptedPdfs::pluck('hash', 'id')->toArray();
        foreach ($hashes as $hash) {
            if(decrypt($hash) == $checkHash) {
                return [
                    'message' => 'Validation Error',
                    'error' => 'Hash already exists.'
                ];
            }
        }

        return "";
    }

    private function findHash($checkHash)
    {
        $hashes = EncryptedPdfs::get(['hash', 'code'])->toArray();
        foreach ($hashes as $hash) {
            if(decrypt($hash["hash"]) == $checkHash) {
                return $hash["code"];
            }
        }

        return [
            'message' => 'File not found',
            'error' => 'File is missing, please check if filename is not changed or not editing in the encrypted file is done.'
        ];
    }
}
