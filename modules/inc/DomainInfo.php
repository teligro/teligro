<?php

namespace teligro;

/**
 * Teligro Domain Info
 *
 * @link       https://wordpress.org/plugins/teligro
 * @since      1.0.0
 *
 * @package    Teligro
 * @subpackage Teligro/modules/inc
 */
class DomainInfo
{
    protected $host;

    public function __construct($url)
    {
        $this->host = Helpers::getURLHost($url);
        return $this;
    }

    function getLocation($ip)
    {
        // Download DB File: https://lite.ip2location.com/file-download, https://lite.ip2location.com/download?id=2
        $db = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'IP2LOCATION-LITE-DB1.BIN';
        $db = new \IP2LocationTeligro\Database($db, \IP2LocationTeligro\Database::FILE_IO);
        $records = $db->lookup($ip, \IP2LocationTeligro\Database::COUNTRY);

        return $records;
    }

    function getIPAddress()
    {
        $domain = 'www.' . $this->host;
        $ip = gethostbyname($domain);
        if ($ip !== $domain)
            return $ip;

        $res = $this->getAddresses($this->host);
        if (count($res) == 0) {
            $res = $this->getAddresses($domain);
        }

        if (isset($res['ip']))
            return $res['ip'];
        elseif (isset($res['ipv6']))
            return $res['ipv6'];

        return false;
    }

    function getAddresses($domain)
    {
        $records = dns_get_record($domain);
        $res = array();
        foreach ($records as $r) {
            if ($r['host'] != $domain) continue; // glue entry
            if (!isset($r['type'])) continue; // DNSSec

            if ($r['type'] == 'A') $res['ip'] = $r['ip'];
            if ($r['type'] == 'AAAA') $res['ipv6'] = $r['ipv6'];
        }
        return $res;
    }
}