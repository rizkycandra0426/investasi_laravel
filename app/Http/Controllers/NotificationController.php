<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationController extends Controller
{
    public function send($user_id, $title, $body)
    {
        $base64String = "ewogICJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCIsCiAgInByb2plY3RfaWQiOiAiaW52ZXN0YXNpLWFwcCIsCiAgInByaXZhdGVfa2V5X2lkIjogIjVmZDNlYzBlZTc5MThkZGI3YTJhOGNjZjMxNzUwNWE1MzZhYzdjZjgiLAogICJwcml2YXRlX2tleSI6ICItLS0tLUJFR0lOIFBSSVZBVEUgS0VZLS0tLS1cbk1JSUV2Z0lCQURBTkJna3Foa2lHOXcwQkFRRUZBQVNDQktnd2dnU2tBZ0VBQW9JQkFRQ3grUGdyZnpFUkxTc1NcbjdnemFFeXVJUUpybzlFcm1YcDh0Q0VwSFBxbFpTZHF3aVFrNEU0OXVzZ2drRk1MbURJdlkxblAzcDlBaDBtbTNcbjhGQTNWZUJJMy84S3R5TE1IWTJnVzRGQnZZTjFYTmVLL212aWhoeGNXVEx2QWt4cWRpQkJGTS9MR1hpdnl4OTJcbldaSUo3aWt1UzV2TGI2ZndlcEY1WlVjQ2REVXh3YmZDQXNuaHlyM1B6TU1lV2FyMTdsMUg0YkFma282aHZ5a3Rcbjc2L3lpQm1ML0hpR1BUUEdLSFBOUGM5TmZOZ2xLQmIzbjd6RDNwMHNWS0VhYnpPcXdUNlA4Qmh3VCtwU0hURm5cbkVPZzZMRHQ5U21nalZsNGhkajhCbG5ZNzhjejZVajA0OUlvRTI2bWgxQlllTHhKZysyQVFiQ3RHYXE0ZjZqMklcbkFOam9NZFVkQWdNQkFBRUNnZ0VBQkNJRVJWV0R0ZUt6dkhnWnVJQzNxV0RaR0JSbW50K0NQdnJFbXMrS05RYUJcbmlFbGJMb3pqVllHa1Ixc0ZSTlluak5NN3JtSlB3RkZSUlY3dUxGa3hGQkFvWUl2VExyNFJXV1BjLzl1NHIwbnRcbldYVjFRYldDa2JFOE1FTG1zL3d6cWlXamVoT2lQNzA2dGVsZXh4NTFCMXdYN1dUSTVJQnZXMlhVbHAyek91K2hcblpvMjRUbk5RalBOMFZGVThxN3RiMXVvd2pVcU54dUdhL1piKzBIbmhwREVTZFNua1c2Z1JrR1hoR090c25HUGxcbnhZSWVFUFdBSm1Pa1QwSDgzc09IYTZzcndNZ0JXRDNVMzdWQjJ1RWZ0c2hwSDJITWhtdXgxbDlXOWNZRk50a1Vcbk40a1BaVTBOTC9TUHByQUxEV0J1V3IrOTFoU1pLZEZRUlNQY3ljVHMwUUtCZ1FEenBtWE9xdHpjaUVWWUgwdWJcbnNiMndEL0FhVXJVQzFlZkE5YzVKTkQxMGliSk8yL1YwcGlPeEZvQ3Q4VE9mUDFkZHZSbURiWVJNRENsd0VSczhcbnV2dWhYc3dKaFZJYktIRGszRE0rWEtxeHZSdERTU0QvRjJKaUZhUmxNTHFNakNzZ3pUWDNEQzV4SUo5SjBKTGhcbktQb3ZZdXd4ZHFNMnpKK2FmbUdzYWZMRUdRS0JnUUM2L2xkUXE2NUhnY05BK3JpQnloMVdVMFhsNDMwaURaVElcbmxvaGhHS05vRGNzRlR3MGF5cktXRHpvcGhOYUg4QUhxTVp4cVRyUnRDSlpDQnBwNVR1RGlOWkQwVEZlUDZGeGNcbjFkMm5KcUY3Z0l1K09tdmpIWHhjMFY5RGVKK1lTc2Q0NXBlMlVhdEZmVXVzYytFSXdyOXFHN1pZRXNGQWM4c3pcbjB3eDQ4SXNacFFLQmdIVWI2WnNvTFBnaTE1Ry9tUXZBcHJmVk1VYTFzaU1teFRuTjY5VHJzdzRza1BPdWJaQWZcbnV0QUhUTnBPTU0yK0dEUUtEQlZMc21jTEFXL0lDUnRybEQ2LzNicEZ4bTBmUndQNWd2ZmFlb0Ryc0FyclAxemNcbjFJRG9maGZWRUJVMTJoVHVEWjRzMExXM1JGaXFVNjY5ajJMdlhTOTVJb0hZWUc1VmlJVGlkN2toQW9HQkFLOTRcbklBTkpOdjlPMnlSTW9YclphSElyTGpvdElLMGx4V245Si9qRklBRnlnQUo1VGJqSVlKRER0VFprdXROUSt0c0NcbnR0NWpBSmdZS0xmSWJvaEs1bWdmRCtqUEFwTzkzWDRZQ2lqaDdVSnhPN0RFTFowdmZCVzFPd21iVlZlWlJGbEZcbk9UUVNxdjlJTmd5YnFKMko4Z2psL0ZQbE5ZYi9vYno5M0lSWVpJOHBBb0dCQU94ZE41bUpHZXNPTGRUR1BHVllcbjFaNWZtVng2SlhxcmhJRVNaU3pDamVsNTY0TjFTNk91RDIrNzRWTGdmL1Z1eFpyWXEwMjlMVW5xdnV3eEVkS3dcbmVZSHFlWU13SDAwNkRTdFE5VVRZS3h1YTZYZGhUWFpackU2dFd4L2JqYXRMQmpUVHFKaUhlVldLeHU3TUxnM2FcblVsYmNwQVgzVW1VVUkwYlB5STlvcXlNMFxuLS0tLS1FTkQgUFJJVkFURSBLRVktLS0tLVxuIiwKICAiY2xpZW50X2VtYWlsIjogImZpcmViYXNlLWFkbWluc2RrLXE0czdiQGludmVzdGFzaS1hcHAuaWFtLmdzZXJ2aWNlYWNjb3VudC5jb20iLAogICJjbGllbnRfaWQiOiAiMTAwNTcwMjE1NDIzMzg3ODE0Njg3IiwKICAiYXV0aF91cmkiOiAiaHR0cHM6Ly9hY2NvdW50cy5nb29nbGUuY29tL28vb2F1dGgyL2F1dGgiLAogICJ0b2tlbl91cmkiOiAiaHR0cHM6Ly9vYXV0aDIuZ29vZ2xlYXBpcy5jb20vdG9rZW4iLAogICJhdXRoX3Byb3ZpZGVyX3g1MDlfY2VydF91cmwiOiAiaHR0cHM6Ly93d3cuZ29vZ2xlYXBpcy5jb20vb2F1dGgyL3YxL2NlcnRzIiwKICAiY2xpZW50X3g1MDlfY2VydF91cmwiOiAiaHR0cHM6Ly93d3cuZ29vZ2xlYXBpcy5jb20vcm9ib3QvdjEvbWV0YWRhdGEveDUwOS9maXJlYmFzZS1hZG1pbnNkay1xNHM3YiU0MGludmVzdGFzaS1hcHAuaWFtLmdzZXJ2aWNlYWNjb3VudC5jb20iLAogICJ1bml2ZXJzZV9kb21haW4iOiAiZ29vZ2xlYXBpcy5jb20iCn0K";
        $decoded = base64_decode($base64String);
        $fireConfig = json_decode($decoded, true);

        $firebase = (new Factory)
            ->withServiceAccount($fireConfig);
        // ->withServiceAccount(base_path('service-account.json'));

        $messaging = $firebase->createMessaging();
        $user = User::find($user_id);

        if($user->fcm_token == null) {
            return response()->json([
                "message" => "User ini belum memiliki fcm_token",
                "user" => $user
            ]);
        }

        $message = CloudMessage::fromArray([
            'token' => $user->fcm_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ]);

        $messaging->send($message);
    }

    public function index(Request $request)
    {
        $base64String = "ewogICJ0eXBlIjogInNlcnZpY2VfYWNjb3VudCIsCiAgInByb2plY3RfaWQiOiAiaW52ZXN0YXNpLWFwcCIsCiAgInByaXZhdGVfa2V5X2lkIjogIjVmZDNlYzBlZTc5MThkZGI3YTJhOGNjZjMxNzUwNWE1MzZhYzdjZjgiLAogICJwcml2YXRlX2tleSI6ICItLS0tLUJFR0lOIFBSSVZBVEUgS0VZLS0tLS1cbk1JSUV2Z0lCQURBTkJna3Foa2lHOXcwQkFRRUZBQVNDQktnd2dnU2tBZ0VBQW9JQkFRQ3grUGdyZnpFUkxTc1NcbjdnemFFeXVJUUpybzlFcm1YcDh0Q0VwSFBxbFpTZHF3aVFrNEU0OXVzZ2drRk1MbURJdlkxblAzcDlBaDBtbTNcbjhGQTNWZUJJMy84S3R5TE1IWTJnVzRGQnZZTjFYTmVLL212aWhoeGNXVEx2QWt4cWRpQkJGTS9MR1hpdnl4OTJcbldaSUo3aWt1UzV2TGI2ZndlcEY1WlVjQ2REVXh3YmZDQXNuaHlyM1B6TU1lV2FyMTdsMUg0YkFma282aHZ5a3Rcbjc2L3lpQm1ML0hpR1BUUEdLSFBOUGM5TmZOZ2xLQmIzbjd6RDNwMHNWS0VhYnpPcXdUNlA4Qmh3VCtwU0hURm5cbkVPZzZMRHQ5U21nalZsNGhkajhCbG5ZNzhjejZVajA0OUlvRTI2bWgxQlllTHhKZysyQVFiQ3RHYXE0ZjZqMklcbkFOam9NZFVkQWdNQkFBRUNnZ0VBQkNJRVJWV0R0ZUt6dkhnWnVJQzNxV0RaR0JSbW50K0NQdnJFbXMrS05RYUJcbmlFbGJMb3pqVllHa1Ixc0ZSTlluak5NN3JtSlB3RkZSUlY3dUxGa3hGQkFvWUl2VExyNFJXV1BjLzl1NHIwbnRcbldYVjFRYldDa2JFOE1FTG1zL3d6cWlXamVoT2lQNzA2dGVsZXh4NTFCMXdYN1dUSTVJQnZXMlhVbHAyek91K2hcblpvMjRUbk5RalBOMFZGVThxN3RiMXVvd2pVcU54dUdhL1piKzBIbmhwREVTZFNua1c2Z1JrR1hoR090c25HUGxcbnhZSWVFUFdBSm1Pa1QwSDgzc09IYTZzcndNZ0JXRDNVMzdWQjJ1RWZ0c2hwSDJITWhtdXgxbDlXOWNZRk50a1Vcbk40a1BaVTBOTC9TUHByQUxEV0J1V3IrOTFoU1pLZEZRUlNQY3ljVHMwUUtCZ1FEenBtWE9xdHpjaUVWWUgwdWJcbnNiMndEL0FhVXJVQzFlZkE5YzVKTkQxMGliSk8yL1YwcGlPeEZvQ3Q4VE9mUDFkZHZSbURiWVJNRENsd0VSczhcbnV2dWhYc3dKaFZJYktIRGszRE0rWEtxeHZSdERTU0QvRjJKaUZhUmxNTHFNakNzZ3pUWDNEQzV4SUo5SjBKTGhcbktQb3ZZdXd4ZHFNMnpKK2FmbUdzYWZMRUdRS0JnUUM2L2xkUXE2NUhnY05BK3JpQnloMVdVMFhsNDMwaURaVElcbmxvaGhHS05vRGNzRlR3MGF5cktXRHpvcGhOYUg4QUhxTVp4cVRyUnRDSlpDQnBwNVR1RGlOWkQwVEZlUDZGeGNcbjFkMm5KcUY3Z0l1K09tdmpIWHhjMFY5RGVKK1lTc2Q0NXBlMlVhdEZmVXVzYytFSXdyOXFHN1pZRXNGQWM4c3pcbjB3eDQ4SXNacFFLQmdIVWI2WnNvTFBnaTE1Ry9tUXZBcHJmVk1VYTFzaU1teFRuTjY5VHJzdzRza1BPdWJaQWZcbnV0QUhUTnBPTU0yK0dEUUtEQlZMc21jTEFXL0lDUnRybEQ2LzNicEZ4bTBmUndQNWd2ZmFlb0Ryc0FyclAxemNcbjFJRG9maGZWRUJVMTJoVHVEWjRzMExXM1JGaXFVNjY5ajJMdlhTOTVJb0hZWUc1VmlJVGlkN2toQW9HQkFLOTRcbklBTkpOdjlPMnlSTW9YclphSElyTGpvdElLMGx4V245Si9qRklBRnlnQUo1VGJqSVlKRER0VFprdXROUSt0c0NcbnR0NWpBSmdZS0xmSWJvaEs1bWdmRCtqUEFwTzkzWDRZQ2lqaDdVSnhPN0RFTFowdmZCVzFPd21iVlZlWlJGbEZcbk9UUVNxdjlJTmd5YnFKMko4Z2psL0ZQbE5ZYi9vYno5M0lSWVpJOHBBb0dCQU94ZE41bUpHZXNPTGRUR1BHVllcbjFaNWZtVng2SlhxcmhJRVNaU3pDamVsNTY0TjFTNk91RDIrNzRWTGdmL1Z1eFpyWXEwMjlMVW5xdnV3eEVkS3dcbmVZSHFlWU13SDAwNkRTdFE5VVRZS3h1YTZYZGhUWFpackU2dFd4L2JqYXRMQmpUVHFKaUhlVldLeHU3TUxnM2FcblVsYmNwQVgzVW1VVUkwYlB5STlvcXlNMFxuLS0tLS1FTkQgUFJJVkFURSBLRVktLS0tLVxuIiwKICAiY2xpZW50X2VtYWlsIjogImZpcmViYXNlLWFkbWluc2RrLXE0czdiQGludmVzdGFzaS1hcHAuaWFtLmdzZXJ2aWNlYWNjb3VudC5jb20iLAogICJjbGllbnRfaWQiOiAiMTAwNTcwMjE1NDIzMzg3ODE0Njg3IiwKICAiYXV0aF91cmkiOiAiaHR0cHM6Ly9hY2NvdW50cy5nb29nbGUuY29tL28vb2F1dGgyL2F1dGgiLAogICJ0b2tlbl91cmkiOiAiaHR0cHM6Ly9vYXV0aDIuZ29vZ2xlYXBpcy5jb20vdG9rZW4iLAogICJhdXRoX3Byb3ZpZGVyX3g1MDlfY2VydF91cmwiOiAiaHR0cHM6Ly93d3cuZ29vZ2xlYXBpcy5jb20vb2F1dGgyL3YxL2NlcnRzIiwKICAiY2xpZW50X3g1MDlfY2VydF91cmwiOiAiaHR0cHM6Ly93d3cuZ29vZ2xlYXBpcy5jb20vcm9ib3QvdjEvbWV0YWRhdGEveDUwOS9maXJlYmFzZS1hZG1pbnNkay1xNHM3YiU0MGludmVzdGFzaS1hcHAuaWFtLmdzZXJ2aWNlYWNjb3VudC5jb20iLAogICJ1bml2ZXJzZV9kb21haW4iOiAiZ29vZ2xlYXBpcy5jb20iCn0K";
        $decoded = base64_decode($base64String);
        $fireConfig = json_decode($decoded, true);

        $firebase = (new Factory)
            ->withServiceAccount($fireConfig);
        // ->withServiceAccount(base_path('service-account.json'));

        $messaging = $firebase->createMessaging();

        $user_id = request()->user_id;
        $user = User::find($user_id);
        if ($user == null) {
            return response()->json([
                "message" => "User tidak ditemukan",
                "user" => $user
            ]);
        }

        $title = request()->title;
        $body = request()->body;

        if ($user->fcm_token == null) {
            return response()->json([
                "message" => "User ini belum memiliki fcm_token",
                "user" => $user
            ]);
        }

        $message = CloudMessage::fromArray([
            'token' => $user->fcm_token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ]);

        $messaging->send($message);

        return response()->json([
            "message" => "OK",
            "user" => $user,
            'token' => $user->fcm_token,
        ]);
    }

    public function store(Request $request)
    {
        if (request()->fcm_token == null) {
            return response()->json([
                "message" => "fcm_token is null, data tidak disimpan"
            ]);
        }
        $user_id = request()->user_id;
        $user = User::find($user_id);
        $user->fcm_token = request()->fcm_token;
        $user->save();

        return response()->json([
            "message" => "OK"
        ]);
    }
}
