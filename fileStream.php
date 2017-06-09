<?php
/*
 * This is a near enough clone of c#'s binary reader/write combined...
 * Currently Unsigned/signed numbers are supported, still needs Unicode support and float support.
 * Written by Hect0r Xorius <sysop@staticpi.net>
 */

// Get Machine Endian, Gets the current machines endian type.
function getMachineEndian() {
    $testint = 0x00FF;
    $p = pack('S', $testint);
    return $testint===current(unpack('v', $p)) ? Endian::LOW:Endian::HIGH;
}

// To Byte Array, Converts a hexadecimal string to a byte array (ints).
function toByteArray($str) {
    $output = array();
    
    for($i = 0; $i < strlen($str) / 2; $i+=2) {
        $output[] = hexdec(substr($str, $i, 2));
    }
    
    return $output;
}

// Hex To String, Converts a hexadecimal string to a ascii string.
function hexToStr($hex) {
    $str = '';
    for($i=0;$i<strlen($hex);$i+=2) { $str .= chr(hexdec(substr($hex,$i,2))); }
    return $str;
}

// Reverse String, Reverses a hexadecimal string.
function reverseStr($bytes_str) {
    $str = "";
    for($i = 0; $i < strlen($bytes_str) / 2; $i++) {
        $str = substr($bytes_str, ($i * 2), 2) . $str;
    }
    return $str;
}

// Reverse Unicode String, Reverses a unicode hexadecimal string.
function reverseUnicodeStr($bytes_str) {
    $str = "";
    for($i = 0; $i < strlen($bytes_str) / 2; $i++) {
        $str .= substr($bytes_str, ($i * 2), 1) . substr($bytes_str, ($i * 2) - 1, 1);
    }
    return $str;
}

// Decimal Hexadecimal UInt8, Converts a UInt8 Value to its correct hexadecimal representation.
function decHexUInt8($in) {
    if((int)$in >= 0 && (int)$in <= 255) {
        $val = dechex((int)$in);
        if(strlen($val) === 1) { $val = '0' . $val; }
        
        return $val;
    }
    throw new Exception('Invalid Unsigned Integer (Size 8 Bits)');
}

// Decimal Hexadecimal Int8, Converts a Int8 value to its correct hexadecimal representation.
function decHexInt8($in) {
    if((int)$in >= -128 && (int)$in <= 127) {
        $val = dechex((int)$in);
        if(strlen($val) === 1) { $val = 'F' . $val; }
        return $val;
    }
    throw new Exception('Invalid Integer (Size 8 Bits)');
}

// Decimal Hexadecimal UInt16, Converts a UInt16 value to its correct hexadecimal representation, along with selected endian.
function decHexUInt16($in, $endian) {
    if((int)$in >= 0 && (int)$in <= 65535) {
        $val = dechex((int)$in);
        while(strlen($val) !== 4) {
            if((int)$endian === (int)Endian::HIGH) { $val = $val . '0'; }
            else { $val = '0' . $val; }
        }
        return $val;
    }
    throw new Exception('Invalid Unsigned Integer (Size 16 Bits)');
}

// Decimal Hexadecimal Int16, Converts a Int16 value to its correct hexadecimal representation, along with selected endian.
function decHexInt16($in, $endian) {
    if((int)$in >= -32767 && (int)$in <= 32766) {
        $val = dechex((int)$in);
        while(strlen($val) !== 4) {
            if((int)$endian === (int) Endian::HIGH) { $val = $val . 'F'; }
            else { $val = 'F' . $val; }
        }
    }
    throw new Exception('Invalid Integer (Size 16 Bits)');
}

// Decimal Hexadecimal UInt32, Converts a UInt32 value to its correct hexadecimal representation, along with selected endian.
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
    throw new Exception('Invalid Unsigned Integer (Size 32 Bits)');
}

// Decimal Hexadecimal Int32, Converts a Int32 value to its correct hexadecimal representation, along with selected endian.
function decHexInt32($in, $endian) {
    if((int)$in >= -2147483648 && (int)$in <= 2147483647) {
        $val = dechex((int)$in);
        while(strlen($val) !== 8) {
            if((int)$endian === (int)Endian::HIGH) { $val = $val . 'F'; }
            else { $val = 'F' . $val; }
        }
        return $val;
    }
    throw new Exception('Invalid Integer (Size 32 Bits)');
}

// Decimal Hexadecimal UInt64, Converts a UInt64 value to its correct hexadecimal representation, along with selected endian.
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
    throw new Exception('Invalid Unsigned Integer (Size 64 Bits)');
}

// Decimal Hexadecimal Int64, Converts a Int64 value to its correct hexadecimal representation, along with selected endian.
function decHexInt64($in, $endian) {
    if((int)$in >= -9223372036854775807 && (int)$in <= -9223372036854775806) {
        $val = dechex((int)$in);
        while(strlen($val) !== 16) {
            if((int)$endian === (int)Endian::HIGH) { $val = $val . 'F'; }
            else { $val = 'F' . $val; }
        }
    }
    throw new Exception('Invalid Integer (Size 64 Bits)');
}

// Endian Class, either high or low.
class Endian {
    const HIGH = 1;
    const LOW = 0;
}
class fileStream {
    private $filename = ''; // Input/Output File Location.
    private $handle = null; // The resource/pointer we're writing to.
    private $pos = 0; // The current position in the stream.
    private $open = false; // private to set if open or closed.
    
    // Checks boolean "open" for true = open, false = closed.
    public function isOpen() { return (bool)$this->open; }
    
    // Construction of the class deals with opening the pointer/resource to the stream.
    public function __construct($file, $perms) {
        $this->filename = $file;
        $this->handle = fopen($file, $perms);
        if(is_resource($this->handle)) {
            $this->open = true;
        }
    }
    
    // Set Position, sets the position in the stream, this is a static value it will go to.
    public function setPosition($pos) {
        $this->pos = (int)$pos;
        fseek($this->handle, (int)$this->pos, SEEK_SET);
    }
    
    // Append Position, takes current stream location and adds whatever static value you give it.
    public function appendPosition($pos) {
        $this->pos += (int)$pos;
        fseek($this->handle, (int)$pos, SEEK_SET);
    }
    
    // Get Position, takes the current position of the stream.
    public function getPosition() { return (int)$this->pos; }
    
    // Read Bytes String, reads a chunk of data into a hexadecimal string.
    public function readBytesStr($len) {
        $str = bin2hex(fread($this->handle, (int)$len));
        $this->pos += (int)$len;
        return $str;
    }
    
    // Read Bytes, reads a chunk of data into a integer array (like byte[] / uint8_t[]).
    public function readBytes($len) {
        $str = bin2hex(fread($this->handle, (int)$len));
        $this->pos += (int)$len;
        return toByteArray($str);
    }
    
    // Read UInt8, reads a unsigned integer of 8 bits.
    public function readUInt8() {
        $str = bin2hex(fread($this->handle, 1));
        $this->pos++;
        return (int)hexdec($str);
    }
    
    // Read Int8, reads a integer of 8 bits.
    public function readInt8() {
        $str = bin2hex(fread($this->handle, 1));
        $this->pos++;
        return (int)hexdec($str);
    }
    
    // Read UInt16, reads a unsigned integer of 16 bits and switches for selected endian.
    public function readUInt16($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 2));
        $this->pos += 2;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    
    // Read Int16, reads a integer of 16 bits and switches for selected endian.
    public function readInt16($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 2));
        $this->pos += 2;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    
    // Read UInt32, reads a unsigned integer of 32 bits and switches for selected endian.
    public function readUInt32($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 4));
        $this->pos += 4;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    
    // Read Int32, reads a integer of 32 bits and switches for selected endian.
    public function readInt32($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 4));
        $this->pos += 4;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    
    // Read UInt64, reads a unsigned integer of 64 bits and switches for the selected endian.
    public function readUInt64($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 8));
        $this->pos += 8;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    
    // Read UInt64, reads a integer of 64 bits and switches for the selected endian.
    public function readInt64($endian = Endian::LOW) {
        $str = bin2hex(fread($this->handle, 8));
        $this->pos += 8;
        return hexdec(((int)getMachineEndian() !== (int)$endian) ? reverseStr($str):$str);
    }
    
    // Read Ascii String, reads a ascii string (unsigned 8) to a string value.
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
    public function writeInt16($int) {
        fwrite($this->handle, hex2bin(decHexInt16((int)$int, getMachineEndian())), 2);
        $this->pos += 2;
    }
    public function writeUInt32($int) {
        fwrite($this->handle, hex2bin(decHexUInt32((int)$int, getMachineEndian())), 4);
        $this->pos += 4;
    }
    public function writeInt32($int) {
        fwrite($this->handle, hex2bin(decHexInt32((int)$int, getMachineEndian())), 4);
        $this->pos += 4;
    }
    public function writeUInt64($int) {
        fwrite($this->handle, hex2bin(decHexUInt64((int)$int, getMachineEndian())), 8);
        $this->pos += 8;
    }
    public function writeInt64($int) {
        fwrite($this->handle, hex2bin(decHexInt64((int)$int, getMachineEndian())), 8);
        $this->pos += 8;
    }
    public function writeAsciiString($str) {
        fwrite($this->handle, $str, strlen($str));
        $this->pos += (int)strlen($str);
    }
    
    // Closes the resource/stream.
    public function close() {
        if(is_resource($this->handle)) {
            fclose($this->handle);
            $this->open = false;
        }
    }
}
?>
