<div class="page">
    <?php
    if ($this->HasAccess('write') && $this->HasAccess('read')) {
        if ($_POST) {
            // only if saving:

            if (_SUBMIT == $_POST['submit']) {
                // check for overwriting

                if ($this->page) {
                    if ($this->page['id'] != $_POST['previous']) {
                        $error = "OVERWRITE ALERT: This page was modified by someone else while you were editing it.<br>\nPlease copy your changes and re-edit this page.";
                    }
                }

                // store

                if (!$error) {
                    $body = str_replace("\r", '', $_POST['body']);

                    // add page (revisions)

                    $this->SavePage($this->tag, $body);

                    // now we render it internally so we can write the updated link table.

                    $this->ClearLinkTable();

                    $this->StartLinkTracking();

                    $dummy = $this->Header();

                    $dummy .= $this->Format($body);

                    $dummy .= $this->Footer();

                    $this->StopLinkTracking();

                    $this->WriteLinkTable();

                    $this->ClearLinkTable();

                    // forward

                    $this->Redirect($this->href());
                }
            }
        }

        // fetch fields

        if (!$previous = $_POST['previous']) {
            $previous = $this->page['id'];
        }

        if (!$body = $_POST['body']) {
            $body = $this->page['body'];
        }

        // preview?

        if (_PREVIEW == $_POST['submit']) {
            $previewButtons = '<input name="submit" type="submit" value="'
                              . _SUBMIT
                              . "\" accesskey=\"s\">\n"
                              . '<input name="submit" type="submit" value="'
                              . _EDIT
                              . "\" accesskey=\"p\">\n"
                              . '<input type="button" value="'
                              . _CANCEL
                              . "\" onClick=\"document.location='"
                              . $this->href('')
                              . "';\">\n";

            $output .= "<div class=\"commentinfo\">Preview</div>\n";

            $output .= $this->FormOpen('edit') . "\n" . '<input type="hidden" name="previous" value="' . $previous . "\">\n" . '<input type="hidden" name="body" value="' . htmlspecialchars($body, ENT_QUOTES | ENT_HTML5) . "\">\n";

            $output .= $this->Format($body);

            $output .= "<br>\n" . $previewButtons . $this->FormClose() . "\n";
        } else {
            // display form

            if ($error) {
                $output .= "<div class=\"error\">$error</div>\n";
            }

            // append a comment?

            if ($_REQUEST['appendcomment']) {
                $body = trim($body) . "\n\n----\n\n--" . $this->UserName() . ' (' . strftime('%c') . ')';
            }

            $output .= $this->FormOpen('edit')
                       . '<input type="hidden" name="previous" value="'
                       . $previous
                       . "\">\n"
                       . '<textarea onKeyDown="fKeyDown()" name="body" style="width: 100%; height: 400px">'
                       . htmlspecialchars($body, ENT_QUOTES | ENT_HTML5)
                       . "</textarea><br>\n"
                       . '<input name="submit" type="submit" value="'
                       . _SUBMIT
                       . '" accesskey="s"> <input name="submit" type="submit" value="'
                       . _PREVIEW
                       . '" accesskey="p"> <input type="button" value="'
                       . _CANCEL
                       . "\" onClick=\"document.location='"
                       . $this->href('')
                       . "';\">\n"
                       . $this->FormClose();
        }

        print($output);
    } else {
        print("<em>You don't have write access to this page.</em>");
    }
    ?>
</div>
