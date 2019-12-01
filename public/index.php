<?php
require_once '../app/entity/Person.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert book</title>
</head>
<body>
<div style="width: 500px; margin: 20px auto;">

    <table width="100%" cellpadding="5" cellspacing="1" border="1">
        <form action="client/book.php?method=createAction" method="post">
            <tr>
                <td>Title:</td>
                <td><input name="params[title]" type="text"></td>
            </tr>
            <tr>
                <td>ISBN:</td>
                <td><input name="params[isbn]" type="text"></td>
            </tr>
            <tr>
                <td>Quantity:</td>
                <td><input name="params[quantity]" type="number"></td>
            </tr>
            <tr>
                <td>Author:</td>
                <td>
                    <select name="params[authors][]" multiple>
                        <?php
                        $person = new Person();
                        $authors = $person->findAll(true);
                        foreach ($authors as $author){
                            ?>
                            <option value="<?php echo $author['id']; ?>">
                                <?php echo $author['firstname'] . ' ' . $author['lastname']; ?>
                            </option>
                        <?php
                        }
                        ?>
                   </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input name="submit_data" type="submit" value="Insert Data"></td>
            </tr>
        </form>
    </table>
</div>
</body>
</html>
