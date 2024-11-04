<?php

namespace App\Traits;

trait Cipher
{
    function mod($a, $b)
    {
        return ($a % $b + $b) % $b;
    }

    function cipher($input, $key, $encipher)
    {
        $keyLen = strlen($key);

        for ($i = 0; $i < $keyLen; ++$i)
            if (!ctype_alpha($key[$i]))
                return ""; // Error

        $output = "";
        $nonAlphaCharCount = 0;
        $inputLen = strlen($input);

        for ($i = 0; $i < $inputLen; ++$i) {
            if (ctype_alpha($input[$i])) {
                $cIsUpper = ctype_upper($input[$i]);
                $offset = ord($cIsUpper ? 'A' : 'a');
                $keyIndex = ($i - $nonAlphaCharCount) % $keyLen;
                $k = ord($cIsUpper ? strtoupper($key[$keyIndex]) : strtolower($key[$keyIndex])) - $offset;
                $k = $encipher ? $k : -$k;
                $ch = chr(($this->mod(((ord($input[$i]) + $k) - $offset), 26)) + $offset);
                $output .= $ch;
            } else {
                $output .= $input[$i];
                ++$nonAlphaCharCount;
            }
        }

        return $output;
    }

    function encipher($input, $key)
    {
        return $this->cipher($input, $key, true);
    }

    function decipher($input, $key)
    {
        return $this->cipher($input, $key, false);
    }
}
