let countAlerts = 0;

/**
 * Create a new select to add a new group for the alert
 */
function addButtonAlert() {
    console.log(count);
    countAlerts += 1;

    fetch(window.location.protocol + "//" + window.location.host + '/wp-content/plugins/plugin-ecran-connecte/public/js/utils/allCodes.php')
    .then(res => res.text())
    .then(data => {
      document.querySelector('select.form-control').innerHTML = data;
    });
}

/**
 * Delete the select
 *
 * @param id
 */
function deleteRowAlert(id) {
    let dele = document.getElementById(id);
    dele.remove();
    let dele2 = document.getElementById(id);
    dele2.remove();
}
