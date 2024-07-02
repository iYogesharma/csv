<?php

    namespace  YS\Csv;

    /**
     * Class Writer
     * @author @iyogesharma
     * @package App\Utils\Classes\Csv
     */
    class Writer extends Csv
    {
        /**
         * @param $headers
         * @param $rows
         * @param $filename
         * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
         */
        public static function download( $headers, $rows, $filename)
        {
            $filePath = tempnam(sys_get_temp_dir(), 'csv_');

            $csv = fopen($filePath, 'w');

            fputs($csv , $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            // send the column headers
            fputcsv( $csv, $headers );

            foreach($rows as $row){
                fputcsv( $csv, $row , "," );
            }

            fclose($csv);

            $response = response()->download($filePath,"$filename.csv");

            $response->headers->setCookie(cookie('fileDownload', 'true',0.3,'/',null,null,false));

            return $response->deleteFileAfterSend(true);
        }

        /**
         * Used if data to export is very big in size
         * the data will get exported to csv and also take
         * care of memory consumption
         * @param $headers
         * @param $rows
         * @param $filename
         * @return \Symfony\Component\HttpFoundation\StreamedResponse
         */
        public static function streamDownload( $headers, $rows, $filename )
        {
            return response()->streamDownload(function () use ($headers, $rows, $filename) {
                $csv = fopen('php://output', 'w+');

                fputs($csv , $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

                // send the column headers
                fputcsv( $csv, $headers );

                foreach($rows as $row){
                    $row =  array_slice( $row, 0, count($headers));
                    fputcsv( $csv, $row , "," );
                }

                fclose($csv);
            }, "{$filename}.csv");
        }

    }
