## CSV

<p>
This package serves as a wrapper for CSV reading logic, handling all file reading automatically. You only need to provide a CSV file, and it will return the file data as an array.

Additionally, this package includes a Ys\Csv\Writer to facilitate downloading CSV files.
</p>

### Read Csv File

```php

    $file = $request->file('csv');

    $reader = new YS\Csv\Reader();

    $reader->importFromPath($file);

    //to read chunck of data

    $reader->chunk(2500)->fetch(true); 

    //the fetch function accept a boolean param bool $assoc with default value false, 
    //$assoc = true will fetch data as associated array with column name as key.
    //$assoc = false will return normal array


    // get number of rows in file $reader->getSize() 

    $rows = $reader->getRecords() // return generator you can interate over rows to access data



```


### Download Csv File  

```php

    Writer::download($headers,$data,'filename');

```