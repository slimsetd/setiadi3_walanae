<?php
$konek=mysqli_connect('localhost', 'root','','setiadi');



 $select_presenter   = mysqli_query($konek, "SELECT max(unique_id) as uniq FROM biblio");
                $presenter_select   = mysqli_fetch_array($select_presenter);
                $numpresenter       = mysqli_num_rows($select_presenter);

                $item    = $presenter_select['uniq'];


                if ($numpresenter != 0) {
                    $item++;
                } 
				
				echo $item;

?>