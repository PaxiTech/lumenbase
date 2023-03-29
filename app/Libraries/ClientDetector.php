<?php

namespace App\Libraries;

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

class ClientDetector
{
    protected $dd;
    public function __construct()
    {
        // AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);
        $this->dd = new \WhichBrowser\Parser(getallheaders());
    }

    public function result()
    {
            return [
                'device'=>$this->dd->device->type,
                'ip'=>$_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'],
                'browser'=>$this->dd->browser->toString(),
                'operator'=>$this->dd->os->toString(),
                'brand'=>$this->dd->device->getManufacturer(),
                'model'=>$this->dd->device->getModel()
            ];
            // $clientInfo = $this->dd->getClient(); // holds information about browser, feed reader, media player, ...
            // $osInfo = $this->dd->getOs();
            // $device = $this->dd->getDeviceName();
            // $brand = $this->dd->getBrandName();
            // $model = $this->dd->getModel();
        
    }
}
