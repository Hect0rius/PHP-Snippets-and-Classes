<?php
/*
 * This is a near enough clone of c#'s binary reader/write combined...
 * Currently I only like supporting Unsigned integers as minus numbers are complicated mess tbf.
 * However any questions or for support message @ sysop@staticpi.net
 * Written by Hect0r Xorius.
 */
function getMachineEndian() {
    $testint = 0x00FF;
    $p = pack('S', $testint);
    return $testint===current(unpack('v', $p)) ? Endian::LOW:Endian::HIGH;
}
function toByteArray($str) {
    $output = array();
    
    for($i = 0; $i < strlen($str) / 2; $i+=2) {
        $output[] = hexdec(substr($str, $i, 2));
    }
    
    return $output;
}
function hex_to_str($hex) {
    $str = '';
    for($i=0;$i<strlen($hex);$i+=2) { $str .= chr(hexdec(substr($hex,$i,2))); }
    return $str;
}
function reverseStr($bytes_str) {
    $str = "";
    for($i = 0; $i < strlen($bytes_str) / 2; $i++) {
        $str = substr($bytes_str, ($i * 2), 2) . $str;
    }
    return $str;
}
function reverseUnicodeStr($bytes_str) {
    $str = "";
    for($i = 0; $i < strlen($bytes_str) / 2; $i++) {
        $str .= substr($bytes_str, ($i * 2), 1) . substr($bytes_str, ($i * 2) - 1, 1);
    }
    return $str;
}
function decHexUInt8($in) {
    if((int)$in >= 0 && (int)$in <= 255) {
        $val = dechex((int)$in);
        if(strlen($val) === 1) {
            $val = '0' . $val;
        }
        
        return $val;
    }
    return false;
}
function decHexUInt16($in, $endian) {
    if((int)$in >= 0 && (int)$in <= 65535) {
        $val = dechex((int)$in);
        while(strlen($val) !== 4) {
            if((int)$endian === (int)Endian::HIGH) {
                $val = $val . '0';
            }
            else {
                $val = '0' . $val;
            }
        }
        
        var_dump($val);
        return $val;
    }
    return false;
}
function decHexUInt32($in, $endian) {
    if((int)$in >= 0 && (int)$in <= 4294967295) {
        $val = dechex((int)$in);
        while(strlen($val) !== 8) {
            if((int)$endian === (int)Endian::HIGH) {
                $val = $val . '0';
            }
            else {
                $val = '0' . $val;
            }
        }
        return $val;
    }
    return false;
}
function decHexUInt64($in, $endian) {
    if((int)$in >= 0 && (int)$in <= 9223372036854775807) {
        $val = dechex((int)$in);
        while(strlen($val) !== 16) {
            if((int)$endian === (int)Endian::HIGH) {
                $val = $val . '0';
            }
            else {
                $val = '0' . $val;
            }
        }
        
        return $val;
    }
    return false;
}
class Endian {
    const HIGH = 1;
    const LOW = 0;
}

class fileStream {
    private $filename = '';
    private $handle = null; 
    private $pos = 0;
    private $open = false;
    
    public function isOpen() { return (bool)$this->open; }
    public function __construct($file, $perms) {
        $this->filename = $file;
        $this->handle = fopen($file, $perms);
        if(is_resource($this->handle)) {
            $this->open = true;
        }
    }
    public function setPosition($pos) {
        $this->pos = (int)$pos;
        fseek($this->handle, (int)$this->pos, SEEK_SET);
    }
    public function appendPosition($pos) {
        $this->pos += (int)$pos;
        fseek($this->handle, (int)$pos, SEEK_SET);
    }
    public function getPosition() { return (int)$this->pos; }
    public function readBytesStr($len) {
        $str = bin2hex(fread($this->handle, (int)$len));
        $this->pos += (int)$len;
        return $str;
    }
    public function readBytes($len) {
        $str = bin2hex(fread($this->handle, (int)$len));
        $this->pos += (int)$len;
        return toByteArray($str);
    }
    public function readUInt8() {
        $str = bin2hex(fread($this->handle, 1));
        $this->pos++;
        return (int)hexdec($str);
    }
    public function readUInt16($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 2));
        $this->pos += 2;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    public function readUInt32($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 4));
        $this->pos += 4;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    public function readUInt64($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 8));
        $this->pos += 8;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    public function readAsciiString($len) {
        $str = fread($this->handle, (int)$len);
        $this->pos += (int)$len;
        return $str;
    }
    
    public function writeByte($byte) {
        fwrite($this->handle, hex2bin(decHexUInt8((int)$byte)), 1);
        $this->pos++;
    }
    public function writeBytes($bytesStr) {
        fwrite($this->handle, hex2bin($bytesStr), 1);
        $this->pos += strlen($bytesStr) / 2;
    }
    public function writeUInt16($int) {
        fwrite($this->handle, hex2bin(decHexUInt16((int)$int, getMachineEndian())), 2);
        $this->pos += 2;
    }
    public function writeUInt32($int) {
        fwrite($this->handle, hex2bin(decHexUInt32((int)$int, getMachineEndian())), 4);
        $this->pos += 4;
    }
     public function writeUInt64($int) {
        fwrite($this->handle, hex2bin(decHexUInt64((int)$int, getMachineEndian())), 8);
        $this->pos += 8;
    }
    public function writeAsciiString($str) {
        fwrite($this->handle, $str, strlen($str));
        $this->pos += (int)strlen($str);
    }
    
    public function close() {
        if(is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
}

?>

