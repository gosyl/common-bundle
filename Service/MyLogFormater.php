<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 26/09/17
 * Time: 12:58
 */

namespace Gosyl\CommonBundle\Service;


use Monolog\Formatter\FormatterInterface;

class MyLogFormater implements FormatterInterface {
    public function format(array $record) {
        // TODO: Implement format() method.
        if($record['channel'] == 'my_log' && isset($record['context']['timestamp'])) {
            return json_encode($record['context']) . PHP_EOL;
        } else {
            return '';
        }
    }

    public function formatBatch(array $records) {
        // TODO: Implement formatBatch() method.
        foreach($records as $key => $record) {
            $records[$key] = $this->format($records);
        }

        return $records;
    }
}