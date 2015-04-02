<?php
/**
 * PHPUtil_CsvHandler
 * 
 * A general purpose CSV Handler for importing and exporting.
 * 
 * @package    csv-handler
 * @author     Crystal Barton <cbarto11@uncc.edu>
 */

if( !class_exists('PHPUtil_CsvHandler') ):
class PHPUtil_CsvHandler
{
	public static $length = 99999;		// The maximum length of a line in CSV file.
	public static $delimiter = ',';		// The field delimiter character.
	public static $enclosure = '"';		// The field enclosure character.
	public static $escape = '\\';		// The field escape character.

	public static $last_error = null;	// The last error recorded by the handler.

	
	/**
	 * Imports a CSV file.
	 * @param   string  $filename  The complete file path and name of the CSV file.
	 * @param   array   $rows      The resulting rows array of values from CSV file.
	 * @return  bool    True if import is successful, otherwise false.
	 */
    public static function import( $filename, &$rows )
    {
    	$headers = null;
		$rows = array();
		
		if( !file_exists($filename) )
		{
        	self::$last_error = 'File does not exist: "'.$filename.'".';
        	return false;
		}
        
        $resource = @fopen( $filename, 'r' );
        
        if( $resource === false )
        {
        	self::$last_error = 'Unable to open file: "'.$filename.'".';
        	return false;
		}
		
		$use_comment_column = false;
        while( $keys = fgetcsv($resource, self::$length, self::$delimiter, self::$enclosure, self::$escape) )
        {
			$keys = array_map( 'trim', $keys );

			if( $headers === null ) 
			{
				$headers = $keys;
				if( (count($headers) > 0) && (($headers[0] === '') || ($headers[0] === '#')) )
					$use_comment_column = true;
				continue;
			}


			$row = array();
			
			$i = 0;
			
			if( $use_comment_column )
			{
				$i++;
				if( (count($keys) > 0) && ($keys[0] === '#') ) continue;
			}
			
			for( ; $i < count($keys); $i++ )
			{
				if( ($i < count($headers)) && ($headers[$i] !== '') )
				{
					$row[$headers[$i]] = $keys[$i];
				}
			}
			
			for( ; $i < count($headers); $i++ )
			{
				$row[$headers[$i]] = '';
			}
			
			array_push( $rows, $row );
        }

        fclose( $resource );
        return true;
    }
    
    
	/**
	 * Exports a CSV file from data.
	 * @param  string  The name of the resulting file.
	 * @param  array   An array of header names for the CSV file.
	 * @param  array   An array of rows with values for each column.
	 */
	public static function export( $filename, &$headers, &$rows )
	{
		$delimiter_esc = preg_quote(self::$delimiter, '/'); 
		$enclosure_esc = preg_quote(self::$enclosure, '/');
		$space_esc = preg_quote(' ', '/');
		
		foreach( $rows as &$row )
		{
			foreach( $row as &$column )
			{
				if( is_array($column) )
				{
					if( count($column) > 1 ):
    				foreach( $column as &$c )
    				{
    					if( preg_match("/(?:${delimiter_esc}|${enclosure_esc}|${space_esc}|\s)/", $c) )
    					{
	    					$c = self::$enclosure.str_replace(self::$enclosure, self::$enclosure.self::$enclosure, $c).self::$enclosure;
	    				}
    				}
    				endif;

					$column = implode( self::$delimiter, $column );
				}
			}
		}
		
     	header( 'Content-type: text/csv' );
 		header( 'Content-Disposition: attachment; filename='.$filename.'.csv' );
 		header( 'Pragma: no-cache' );
 		header( 'Expires: 0' );
		
		$outfile = fopen( 'php://output', 'w' );
		
		fputcsv( $outfile, $headers );
		
		for( $i = 0; $i < count($rows); $i++ )
		{
			fputcsv( $outfile, $rows[$i] );
		}
		
		fclose( $outfile );
		exit;
    }

} // if( !class_exists('PHPUtil_CsvHandler') ):
endif; // class PHPUtil_CsvHandler

