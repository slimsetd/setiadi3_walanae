                    <div class="row todo">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="toolbar toolbar--inner">
                                    <div class="toolbar__label">New Collection</div>
                                </div>

                                <div class="listview listview--bordered">

                                    <?php
                                    $current_book = "SELECT b.biblio_id, b.title, b.notes, b.image, b.gmd_id, b.publish_year, b.publisher_id, pa.publisher_name, 
                                                          b.publish_place_id, pl.place_name,pl.place_id,
                                                          ba.biblio_id, ba.author_id, g.gmd_id, g.gmd_name,
                                                          ma.author_id, ma.author_name FROM biblio AS b 
                                                          LEFT JOIN biblio_author AS ba ON ba.biblio_id = b.biblio_id 
                                                          LEFT JOIN mst_author AS ma ON ma.author_id = ba.author_id
                                                          LEFT JOIN mst_gmd AS g ON g.gmd_id = b.gmd_id
                                                          LEFT JOIN mst_publisher AS pa ON pa.publisher_id = b.publisher_id
                                                          LEFT JOIN mst_place AS pl ON pl.place_id = b.publish_place_id
                                                          ORDER BY b.biblio_id DESC LIMIT 0,7";
                                    $current_book_q = $dbs->query($current_book);
                                    while ($current_book_d = $current_book_q->fetch_assoc()) {
                                        if (is_null($current_book_d['image'])) {
                                            $img = 'default/image.png';
                                        } else {
                                            $img = 'docs/' . $current_book_d['image'];
                                        }

                                        if (is_null($current_book_d['notes'])) {
                                            $notes = '<i style="color:#f00;">No Description</i>';
                                        } else {
                                            $notes = substr($current_book_d['notes'], 0, 80) . '... (<a href="?p=show_detail&id=' . $current_book_d['biblio_id'] . '">Read More</a>)';
                                            $titlecut = substr($current_book_d['title'], 0, 1) . '';
                                        }

                                        $html_str  = '<div class="listview__item">
                                                        <div class="checkbox checkbox--char todo__item">
                                                            <input type="checkbox" id="custom-checkbox-1">
                                                            <label class="checkbox__char bg-blue" for="custom-checkbox-1"><img class="avatar-img" src="' . SWB . 'images/' . $img . '" alt=""></label>
                                                            
                                                            <div class="listview__content">
                                                                <div class="listview__heading">' . $current_book_d['title'] . '</div>
                                                                <p>' . $current_book_d['author_name'] . '</p>
                                                                <p>' . $current_book_d['publisher_name'] . ': ' . $current_book_d['place_name'] . ', ' . $current_book_d['publish_year'] . '</p>
                                                            </div>

                                                            <div class="listview__attrs">
                                                                <span>' . $current_book_d['gmd_name'] . '</span>
                                                                <span><a href="?p=show_detail&id=' . $current_book_d['biblio_id'] . '">See Detail</a></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- end -->';

                                        // Set PrintOut
                                        echo $html_str;
                                    }
                                    ?>
                                    <a href="index.php?keywords=&search=search" class="view-more"><?php echo __('View More'); ?></a>
                                </div>
                            </div>
                        </div>