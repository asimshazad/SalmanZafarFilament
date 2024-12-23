<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use OpenAI;
use Illuminate\Support\Facades\Cache;

class OpenAIController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
        ]);

        //$normalizedQuery = $this->normalizeQuery($request->messages['0']['content']);
        $cachedResponse = Cache::get($request->messages['0']['content']);
        if ($cachedResponse) {
            return response()->json(['response' => $cachedResponse]);
        }
        try {
            $postFields = [
                'contents' => [
                    [
                        'parts' => [
                           ['text' => $request->messages['0']['content']],
                        ],
                    ],
                ],
            ];
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . config('aiclass.gemini_api_key'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postFields),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false 
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $responseData = json_decode($response, true);
            Cache::forever($request->messages['0']['content'], $responseData['candidates'][0]['content']['parts'][0]['text']);
            return response()->json(['response' => $responseData['candidates'][0]['content']['parts'][0]['text']]);
            // $client = OpenAI::client(env('OPENAI_API_KEY'));
            // //$response = $client->models()->list(); // return list of models
            // $result = $client->chat()->create([
            //     'model' => 'gpt-3.5-turbo',
            //     'messages' => $request->messages
            // ]);
            // \Log::info([
            //     'output' => $result->choices[0]
            // ]);
            // $response = $result->choices[0]->message->content;
            // return response()->json(['response' => $response]);
            // $stream = $client->chat()->createStreamed([
            //     'model' => 'gpt-3.5-turbo',
            //     'messages' => $request->messages,
            // ]);
            // \Log::info([
            //     'output' => $stream
            // ]);

            // $response = '';
            // foreach ($stream as $chunk) {
            //     \Log::info([
            //         'choices' => $chunk['choices'][0]
            //     ]);
            //     $response .= $chunk['choices'][0]['delta']['content'] ?? '';
            // }

            // return response()->json(['response' => $response]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function normalizeQuery($query)
    {
        // return strtolower(trim(preg_replace('/\s+/', ' ', $query)));
        $query = strtolower($query);

        // // Remove punctuation
        // $query = preg_replace('/[^\w\s]/', '', $query);

        // // Tokenize and remove common stop words
        // $stopWords = ['can', 'you', 'me', 'about', 'in', 'the', 'a', 'is', 'on', 'and', 'or', 'for'];
        // $words = array_diff(explode(' ', $query), $stopWords);
        // return implode(' ', $words);
        return $query;
    }
}
