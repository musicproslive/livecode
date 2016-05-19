<?php

class Result {

    /**

     * data members

     */

    private $query;



    private $database_name;



    private $connection;



    private $result;



    public function __construct ($query, $db_name, $connection) {

        $this->query = $query;

        $this->connection = $connection;

        $this->database_name = $db_name;

        mysql_selectdb ($db_name, $connection) or

        die (mysql_error () . " Error no:" . mysql_errno () . "<br />$query");



        $this->result = mysql_query ($query, $connection) or

        die (mysql_error () . " Error no:" . mysql_errno () . "<br />$query");

        /**

         * check if contains SQL_CALC_FOUND_ROWS

         */

        if (stristr ($query, "SQL_CALC_FOUND_ROWS")) {

            $msg = "No need to use SQL_CALC_FOUND_ROWS.";

            die ($msg);

        }

    }



    public function __destruct () {

        $this->close ();

    }



    public function get_row ($result_type = MYSQL_NUM, $print = false) {

		if (true === $print) {

			echo "<p>DBNAME = " . $this->database_name . "</p>\r\n";

			//echo "<p>Connection DBNAME = " . $this->connection->get_connection () . "</p>\r\n";

			echo "<p>" . $this->query . "</p>\r\n";

		}

        return mysql_fetch_array ($this->result, $result_type);

    }



    public function get_database_name () {

        return $this->database_name;

    }



    public function get_num_fields () {

        return mysql_num_fields ($this->result);

    }

    /**

     * // //////////////////////////////////////////////////////////////////

     * // For select queries only

     * // //////////////////////////////////////////////////////////////////

     */

    public function get_num_rows () {

        return mysql_num_rows ($this->result);

    }



    public function get_num_affected () {

        return mysql_affected_rows ($this->connection);

    }

    /*// //////////////////////////////////////////////////////////////////*/

    public function get_insert_id () {

        return mysql_insert_id ($this->connection);

    }

    /**

     * // //////////////////////////////////////////////////////////////////

     * // Calculate total number of records if a limit clause present

     * // Useful for calculating number of pages in versions < 4

     * // Unreliable results if DISTINCT used

     * // //////////////////////////////////////////////////////////////////

     */

    public function get_unlimited_number_rows () {

        $number = 0;



        $versionnumber = $this->find_version_number ();

        // only need leftmost number

        $version = substr ($versionnumber, 0, 1);

        // CHECK SELECT

        if (!$this->check_for_select ()) {

            $msg = "Illegal method call - not a SELECT query";

            die ($msg);

        }

        /**

         * check for limit clause

         */

        $tempsql = strtoupper ($this->query);



        $end = strpos ($tempsql, "LIMIT");



        if ($end === false) {

            /**

             * no limit clause

             */

            $number = mysql_num_rows ($this->result);

        } elseif ($version < 4) {

            $number = $this->count_version_three ($end);

        } else {

            /**

             * version 4 or higher use SQL_CALC_FOUND_ROWS function

             */

            $number = $this->count_version_four ();

        }



        return $number;

    }



    public function get_field_names () {

        $fieldnames = array ();



        if (isset ($this->result)) {

            $num = mysql_numfields ($this->result);



            for ($i = 0; $i < $num; $i++) {

                $meta = mysql_fetch_field ($this->result, $i) or

                die (mysql_error () . " Error no:" . mysql_errno () . "<br />$query");



                $fieldnames [$i] = $meta->name;

            }

        }



        return $fieldnames;

    }



    public function find_version_number () {

        /**

         * mysql_get_server_info

         */

        return mysql_get_server_info ($this->connection);

    }



    /**

     * // //////////////////////////////////////////////////////////////////

     * // private methods

     * // //////////////////////////////////////////////////////////////////

     */

    private function check_for_select () {

        $bln = true;



        $strtemp = trim (strtoupper ($this->query));



        if (substr ($strtemp, 0, 6) != "SELECT") {

            $bln = false;

        }



        return $bln;

    }



    private function close () {

        if (isset ($this->result)) {

			$query_words = explode (' ',trim($this->query)); //query_words is an array of words that make up the query

			if ($query_words[0] == "SELECT"){				 //checks to see if the first word is SELECT and frees the

            	mysql_free_result ($this->result);			 //data if the query was a select

            	unset ($this->result);

			}

        }

    }



    private function count_version_four () {

        $tempsql = trim ($this->query);

        /**

         * insert SQL_CALC_FOUND_ROWS

         */

        $insertstr = " SQL_CALC_FOUND_ROWS ";

        /**

         * already know it starts with "SELECT"

         */

        $tempsql = substr_replace ($tempsql, $insertstr, 6, 1);

        $record_set = mysql_query ($tempsql, $this->connection) or

        die (mysql_error () . " Error no:" . mysql_errno ());



        $tempsql = "SELECT FOUND_ROWS ()";

        $record_set = mysql_query ($tempsql) or

        die (mysql_error () . " Error no:" . mysql_errno ());



        $row = mysql_fetch_row ($record_set);

        $number = $row [0];

        /**

         * dispose of $record_set

         */

        mysql_free_result ($record_set);



        return $number;

    }



    private function count_version_three ($end) {

        $tempsql = strtoupper ($this->query);

        /**

         * check for DISTINCT - will throw things off

         */

        if (!strpos ($tempsql, "DISTINCT")) {

            /**

             * create recordset

             */

            $start = strpos ($tempsql, "FROM");

            $numchars = $end - $start;

            $countsql = "SELECT COUNT (*) ";

            $countsql .= substr ($this->query, $start, $numchars);

            $record_set = mysql_query ($countsql, $this->connection) or

            die (mysql_error () . " Error no:" . mysql_errno ());



            $row = mysql_fetch_row ($record_set);

            $number = $row [0];

            /**

             * dispose of $record_set

             */

            mysql_free_result ($record_set);

        } else {

            $msg = "Using keyword DISTINCT, " . "calculate total number manually.";

            die ($msg);

        }



        return $number;

    }

}



?>