<?php
/**
Plugin Name: WP Create Forms Save to DB
Description: Creates forms and saves them to the database
Version: 1.0
Author: Kacper Kowalczyk
License: GPLv2 or later
Text Domain: wp-create-forms-save-to-db
*/?>

<style>
  #table table {
    border: 1px solid black;
  }

  #table tr {
    border: 1px solid black;
  }

  #table td {
    width: 150px;
    border: 1px solid blue;
  }

  #forms {
    display: none;
  }

  #submitForm {
    float: right;
  }
</style>

<?php
function DBForms_settings()
{
  add_option('DBForms_option_name', 'DBForms');
  register_setting('DBForms_options_group', 'DBForms');
}
add_action('admin_init', 'DBForms_settings');

function DBForms_add_options_page()
{
  add_menu_page('DBForms', 'DBForms', 'manage_options', 'DBForms', 'DBForms_options_page');
}
add_action('admin_menu', 'DBForms_add_options_page');

function DBForms_options_page()
{
  global $wpdb;
  if (empty($wpdb->get_results('SHOW TABLES LIKE "imiona_nazwiska"'))) {
    $wpdb->get_results('
    CREATE TABLE `imiona_nazwiska` (`ID` BIGINT(20) NOT NULL AUTO_INCREMENT , `imie` TEXT NOT NULL , `nazwisko` TEXT NOT NULL , PRIMARY KEY (`ID`));
    ');
    echo "Table was created succesfully, Please refresh!";
  } else {


    ?>
    <h2>FORMS TO DB</h2>
    <div id='button'>
      <label for="isChecked">Do you want to add another person?
        <input type="checkbox" id="isChecked"></input></label>
    </div>
    <br>
    <div id="forms">
      <form action="admin.php?page=DBForms" method="POST">
        <label for='imieForms'>Imie <br>
          <input type="text" id='imieForms' name='imieForms'></input></label><br>
        <label for='NazwiskoForms'>Nazwisko <br>
          <input type="text" id='nazwiskoForms' name='nazwiskoForms'></input></label><br><br>
        <input type="submit" name='submitFormsDB' value="Submit" id="submitFormsDB"><br><br>
        <?php
        if (isset($_POST['imieForms'], $_POST['nazwiskoForms'])) {
          $imie = $_POST['imieForms'];
          $nazwisko = $_POST['nazwiskoForms'];
          if (!empty($imie && $nazwisko)) {
            $wpdb->get_results("INSERT INTO `imiona_nazwiska` (`ID`, `imie`, `nazwisko`) VALUES (NULL, '$imie', '$nazwisko');");
            echo "Operation completed successfully!";
          } else {
            echo "Operation did not complete, because fields were empty.";
          }

        }
        ?>
      </form>
    </div>
    <div>
      <div id='table'>
        <form method="post" action="options.php">
          <?php settings_fields('DBForms_options_group');
          echo "<table>
        <tr>
          <td>ID</td>
          <td>Imie</td>
          <td>Nazwisko</td>
        </tr>";
          $result = $wpdb->get_results("SELECT * FROM imiona_nazwiska");
          foreach ($result as $print) { ?>
            <tr>
              <td>
                <?php echo $print->ID; ?>
              </td>
              <td>
                <?php echo $print->imie; ?>
              </td>
              <td>
                <?php echo $print->nazwisko; ?>
              </td>
            </tr>
          </form>
        </div>
      </div>
      <?php
          }
          ?>

    <script>
      const table = document.getElementById('table')
      const button = document.querySelector('#isChecked')
      const forms = document.getElementById('forms')
      const submit = document.querySelector('#submitFormsDB')
      button.onclick = () => {
        if (table.style.display == 'none') {
          table.style.display = 'inline-block',
            forms.style.display = 'none'
        } else {
          table.style.display = 'none',
            forms.style.display = 'inline-block'
        }
      }


    </script>
    <?php
  }
}
