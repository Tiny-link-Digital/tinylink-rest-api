<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Support\Carbon;

class Url extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'urls';
    protected $dates = [
        'created_at',
        'expires_at'
    ];

    /**
     * Gera a hash da URL encurtada.
     *
     * @param string $url
     * @param int $step
     * @param string $_id
     *
     * @return string
     */
    public function getHash(): string
    {
        $hash = "";

        $now = Carbon::now()->toDateTimeString();
        $url_encrypted = md5($this->url.$now);

        $encryption_letters = str_split($url_encrypted);
        $encryption_length = count($encryption_letters);

        $rand_index_history = [];
        $counter = 0;
        while ($counter < 6) {
            $rand_index = rand(1, $encryption_length) - 1;

            if (!in_array($rand_index, $rand_index_history)) {
                $hash .= $encryption_letters[$rand_index];

                array_push($rand_index_history, $rand_index);

                $counter++;
            }
        }

        return $this->checkHashUniquity($hash);
    }

    /**
     * Verifica se o hash gerado é único.
     *
     * @param string $hash
     *
     * @return string
     */
    private function checkHashUniquity(string $hash): string
    {
        $repeated_hash = Url::where('hash', '=', $hash)->get();

        if (count($repeated_hash) > 0) {
            return "";
        }

        return $hash;
    }

    /**
     * Retorna a data de expiração dessa URL.
     *
     * @return Illuminate\Support\Carbon
     */
    public function getExpirationDate(): Carbon
    {
        $expiration_date = new Carbon();

        $expiration_date->addYears(1);

        return $expiration_date;
    }
}
