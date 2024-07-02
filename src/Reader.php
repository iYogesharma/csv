<?php

    namespace  YS\Csv;

    use Illuminate\Support\LazyCollection;

    /**
     * Class Reader
     * @author @iyogesharma
     */
    class Reader extends Csv
    {
        /** @var LazyCollection containing all file rows */
        protected $fileCollection;

        /** @var array holds header row  */
        protected $headers;

        /** @var LazyCollection containing all rows of sheet except header */
        protected $records = [];

        /** @var int number of rows in sheet */
        protected $size;

        /** @var array header keys to fetch records as associated array */
        protected $keys = [];


        /**
         * Import file from path in read context
         * @param $filePath
         */
        public function importFromPath( $filePath )
        {
            $this->fileCollection =  LazyCollection::make(function () use ($filePath) {
                
                $handle = fopen($filePath, 'r');

                while ( ($line = fgetcsv($handle)) !== false ) {
                    yield $line;
                }
               
            });
        }

        /**
         * chunk size if you want to read data in
         * chunks in order to save memory
         * @param int $size
         * @return $this
         */
        public function chunk( int $size )
        {
            $this->fileCollection = $this->fileCollection->chunk( $size );
            return $this;
        }

        /**
         * Fetch rows from csf as associated array
         * with first column as keys for associated array
         * @return void
         */
        public function fetchAssoc()
        {
            $this->fileCollection = $this->fileCollection
                ->each(function ($lines)  {
                    foreach ($lines as $k => $line) {
                        if($k === 0 )
                        {
                            $bom = pack('CCC', 0xEF, 0xBB, 0xBF);

                            if (substr($line[0], 0, 3) === $bom) {
                                $line[0] = substr($line[0], 3);
                            }

                            $this->setHeaders($line);
                            
                            array_walk( $line, function( &$value, $key) {
                                $value = explode('(', $value) [0];
                                $value = str_replace('*', '', $value);
                                $value = strtolower(str_replace(' ', '_', $value));
                            });
                            $this->setKeys($line);
                        }
                        else
                        {
                            $this->records[] = array_combine ( $this->getKeys(), $line ) ;
                        }
                    }
                    $this->setSize( count($this->records));
                });
        }

        /**
         * Fetch rows from csv as normal array with optional argument
         * bool $assoc if you want rows in associated array
         * @param bool $assoc
         * @return void
         */
        public function fetch( $assoc = false )
        {
            if( $assoc ) {
                $this->fetchAssoc();
            } else {
                $this->fileCollection = $this->fileCollection
                    ->each(function ($lines) {
                        foreach ($lines as $k => $line) {
                            if($k === 0 )
                            {
                                $bom = pack('CCC', 0xEF, 0xBB, 0xBF);

                                if (substr($line[0], 0, 3) === $bom) {
                                    $line[0] = substr($line[0], 3);
                                }
                                // $line[0] = preg_replace ("/ ^ $bom /", '', $line[0]);
                               
                                $this->setHeaders($line);
                            }
                            else
                            {
                                $this->records[] = $line;
                            }
                        }
                        $this->setSize( count($this->records));
                    });
            }

        }

        /**
         * Get Records from sheet as generator
         * @return \Generator
         */
        public function getRecords()
        {
            foreach( $this->records as $r )
            {
                yield $r;
            }
        }

    }
