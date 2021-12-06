<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Models\MeterReaderTrackNames;
use App\Models\MeterReaderTracks;
use App\Models\IDGenerator;

class MeterReaderTracksAPI extends Controller {
    public $successStatus = 200;

    public function saveTrackNames(Request $request) {
        $trackNames = MeterReaderTrackNames::where('TrackName', $request['TrackName'])->first();

        if ($trackNames != null) {
            return response()->json(['res' => 'exists'], $this->successStatus);
        } else {
            $trackNames = new MeterReaderTrackNames;
            $trackNames->id = IDGenerator::generateIDandRandString();
            $trackNames->TrackName = $request['TrackName'];
            $trackNames->save();

            return response()->json($trackNames, $this->successStatus);
        }
    }

    public function saveTracks(Request $request) {
        $track = MeterReaderTracks::where('Latitude', $request['Latitude'])
                    ->where('Longitude', $request['Longitude'])
                    ->where('Captured', $request['created_at'])
                    ->first();

        if ($track != null) {
            return response()->json(['res' => 'exists'], $this->successStatus);
        } else {
            $track = new MeterReaderTracks;
            $track->id = IDGenerator::generateIDandRandString(35);
            $track->TrackNameId = $request['TrackNameId'];
            $track->Latitude = $request['Latitude'];
            $track->Longitude = $request['Longitude'];
            $track->Captured = $request['created_at'];
            $track->save();

            return response()->json($track, $this->successStatus);
        }
    }
}

?>