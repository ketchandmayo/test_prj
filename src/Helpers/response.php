<?php
function response($success = false, $data = []): bool|string
{
    $response["success"] = $success;
    if(!empty($data)) {
        $response['result'] = $data;
    }
    return json_encode($response);
}

function invalidRequest()
{
    return response(false, ["error" => "invalid request"]);
}

function error($error)
{
    return response(false, ["error" => $error]);
}