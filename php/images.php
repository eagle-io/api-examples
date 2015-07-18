<?php

// EXAMPLE images.php
// Extracts URLs for the 10 most recently uploaded images in your account
// Read the API Documentation @ http://docs.eagle.io/en/latest/api/index.html
// --------------------------------------------------------------------------


// Include HTTPFUL Rest Client Package from http://phphttpclient.com
include('./lib/httpful.phar');

$api_key      = 'YOUR_API_KEY_HERE'; // You can generate an API key from User preferences
$api_path     = 'https://eagle.io/api/v1/';
$api_resource = 'nodes';
$api_query    = 'filter=_class($match:io.eagle.models.node.Attachment)&attr=name,fileUrl,filePreviewUrl&sort=createdTime(DESC)&limit=10';
// optionally add an expiry period in minutes for the image urls.
// $api_query .= '&expiry=60';


// NOTE:
// The $api_query in the example above will return the 10 most recently uploaded attachment nodes available to the user (requires ATTACHMENT_READ permission)
// and will include name, fileUrl, filePreviewUrl attributes.
// You could also return only images underneith a specific node (ie. Location, AttachmentSource) by additionally specifying a parentId as part of the filter criteria
// eg. filter=_class($match:io.eagle.models.node.Attachment),parentId($eq:1234567890abcdef)


// build the uri path ensuring query string is url encoded
$uri = $api_path . $api_resource . '/?' . urlencode($api_query);
// Automatically encode response as JSON
\Httpful\Httpful::register(\Httpful\Mime::JSON, new \Httpful\Handlers\JsonHandler(array('decode_as_array' => true)));
// Send request
$response = \Httpful\Request::get($uri)
    ->expectsType('json')
    ->addHeader('x-api-key', $api_key)
    ->send();

// Ensure we get a 200 OK response
if ($response->code != 200) {
    echo ($response->body['error']['message']);
    die();
}

$html_content = '';
foreach ($response->body as $image) {
    // skip attachments that do not contain a preview image
    if (!isset($image['filePreviewUrl'])) { continue; }
    // NOTE: the preview image dimensions may change without notice. We recommend you generate your own preview images from the original file.
    $html_content .= '<a class="preview" href="' . $image['fileUrl'] . '" target="_blank" title="' . $image['name'] . '"><img src="' . $image['filePreviewUrl'] . '"></a>';
}

// Build html page and display
$html  = '<head><style>.preview {margin: 0 10px 10px 0; float: left;}</style></head>';
$html .= '<body><h1>eagle.io api images example</h1>';
$html .= $html_content;
$html .= '</body>';
echo $html;

?>