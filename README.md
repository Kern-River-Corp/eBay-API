eBay API
========
[eBay API Call Index](<http://developer.ebay.com/devzone/xml/docs/reference/ebay/> "List of all eBay API Calls (Not all included yet)")
> Create an eBay specific XML_API_Call, setting correct
headers by appending credentials from database, using the correct
URL according to sandbox/production, and using the right root
element based on the call name.
Can work with **multiple stores** and in **production**
or **sandbox** environments.

Uses [Super User Core](<https://github.com/shgysk8zer0/core/>)'s [XML_API_Call](<https://github.com/shgysk8zer0/core/blob/master/xml_api_call.php>)
for extending `DOMDocument` & `DOMElement`, as well as setting custom headers in
cURL requests.
## Usage
```
// Construct class for desired eBay API Call
$req = new \eBay_API\Request\CallName($args);

// Chain together creating elements using magic `__call()` method
$req->$childNode(
    $ChildNodeContent,
    [
        $attributeName => $attributeValue,
        ...
    ],
    $namespace
)->$secondChild([
    $nodeName => [
        $decendantNode => $contnet
    ]
])->send($output);
// Returns SimpleXMLElement and saves request & response to
// $output_YYYY-mm-ddTHH:ii_(request|response).xml
```
## Also allows for arbitrary eBay API Calls using
```
// Functions similar to above
$req = new \eBay_API\eBay_API_Call($store, $callname, $sandbox);
$req->$childNode(
    $ChildNodeContent,
    [
        $attributeName => $attributeValue,
        ...
    ],
    $namespace
)->$secondChild([
    $nodeName => [
        $decendantNode => $contnet
    ]
])->send();
```
## Contacting
* [Report Issues](<https://github.com/Kern-River-Corp/eBay-API/issues/new> "GitHub Issues")
* [Email Developer](<mailto:chris@kernrivercorp.com> "Send me an email")
