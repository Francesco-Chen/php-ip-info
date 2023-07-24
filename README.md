# php-ip-info
Lightweight PHP tool for geolocation and ASN lookup. Retrieves IP address details in JSON format using the trusted database from https://iptoasn.com/. Enhance your applications with accurate geolocation information and network insights effortlessly.

# Database
You need download "ip2asn-combined.tsv" from https://iptoasn.com/data/ip2asn-combined.tsv.gz

**Example Response:**
```
curl https://ipinfo.example.com/?ip=1.1.1.1
```
```json
{
    "ip": "1.1.1.1",
    "country": "USA",
    "country_code": "US",
    "org": "AS13335 CLOUDFLARENET",
    "asn": "13335",
    "isp": "CLOUDFLARENET",
    "range_start": "1.1.1.0",
    "range_end": "1.1.1.255"
}
