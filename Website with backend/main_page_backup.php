<style>
  <?php include 'main_page_backup.css'; ?>
</style>
<script src="script_backup.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script src="https://www.google.com/recaptcha/enterprise.js?render=6LdsR84pAAAAAOSyV5zT4LzrZPldUWdysiAYVrK3"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

<head>
  <!-- Add the reCAPTCHA script in the head section -->
  <script src="https://www.google.com/recaptcha/enterprise.js?render=6LdsR84pAAAAAOSyV5zT4LzrZPldUWdysiAYVrK3"></script>
</head>

<?php

$username = "user";
$password = "userview";
$database = "db2";

try {
  $pdo = new PDO("mysql:host=localhost;database=$database", $username, $password);
  // Set the PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("ERROR: Could not connect. " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data visualizer</title>
</head>

<body>


  <?php
  // Attempt select query execution
  try {
    $sql = "SELECT organizacija.ORGANIZACIJA_PAVADINIMAS, SUM(lankomumas.LANKOMUMAS_PERZIUROS) AS Total_Perziuros 
        FROM db2.lankomumas 
        JOIN db2.rinkinys ON lankomumas.RINKINYS_ID = rinkinys.RINKINYS_ID 
        JOIN db2.organizacija ON rinkinys.ORGANIZACIJA_ID = organizacija.ORGANIZACIJA_ID 
        GROUP BY organizacija.ORGANIZACIJA_PAVADINIMAS";

    $sql1 = "SELECT organizacija.ORGANIZACIJA_PAVADINIMAS, rinkinys.RINKINYS_PAVADINIMAS, SUM(lankomumas.LANKOMUMAS_PERZIUROS) AS LANKOMUMAS_PERZIUROS, rinkinys.RINKINYS_NUORODA
            FROM db2.lankomumas 
            JOIN db2.rinkinys ON lankomumas.RINKINYS_ID = rinkinys.RINKINYS_ID 
            JOIN db2.organizacija ON rinkinys.ORGANIZACIJA_ID = organizacija.ORGANIZACIJA_ID 
            GROUP BY organizacija.ORGANIZACIJA_PAVADINIMAS, rinkinys.RINKINYS_PAVADINIMAS, rinkinys.RINKINYS_NUORODA";

    $sql3 = "SELECT 
        RINKINYS.RINKINYS_PAVADINIMAS AS Rinkinys_Pavadinimas,
        ORGANIZACIJA.ORGANIZACIJA_PAVADINIMAS AS Organizacija,
        L.Lankomumas_Perziuros AS Lankomumas_Perziuros,
        RINKINYS.RINKINYS_ATNAUJ_DATA AS Rinkinys_Atnauj_Data,
        RINKINYS.RINKINYS_NUORODA AS Nuoroda
    FROM 
        db2.RINKINYS
    JOIN 
        db2.ORGANIZACIJA ON RINKINYS.ORGANIZACIJA_ID = ORGANIZACIJA.ORGANIZACIJA_ID
    LEFT JOIN 
        (SELECT 
            RINKINYS_ID,
            MAX(Lankomumas_Perziuros) AS Lankomumas_Perziuros
        FROM 
            db2.LANKOMUMAS
        GROUP BY 
            RINKINYS_ID) L ON RINKINYS.RINKINYS_ID = L.RINKINYS_ID";
    $sql4 = "SELECT 
    TIPAS.TIPAS_KATEGORIJA AS Rinkinys_Tip,
    COUNT(*) AS Count
FROM 
    DB2.RINKINYS
JOIN 
    DB2.SUSIDARO ON RINKINYS.RINKINYS_ID = SUSIDARO.RINKINYS_ID
JOIN 
    DB2.TIPAS ON SUSIDARO.TIPAS_ID = TIPAS.TIPAS_ID
GROUP BY 
    TIPAS.TIPAS_KATEGORIJA";

    $sql41 = "SELECT
    RINKINYS.RINKINYS_IKEL_DATA AS Ikel_Data,
    RINKINYS.RINKINYS_ATNAUJ_DATA AS Atn_Data,
      TIPAS.TIPAS_KATEGORIJA AS Tipas,
      RINKINYS.RINKINYS_PAVADINIMAS AS Rinkinys_Pavadinimas,
      RINKINYS.RINKINYS_NUORODA AS Rinkinys_Nuoroda
  FROM 
      DB2.RINKINYS
  JOIN 
      DB2.SUSIDARO ON RINKINYS.RINKINYS_ID = SUSIDARO.RINKINYS_ID
  JOIN 
      DB2.TIPAS ON SUSIDARO.TIPAS_ID = TIPAS.TIPAS_ID
  GROUP BY 
      TIPAS.TIPAS_KATEGORIJA, RINKINYS.RINKINYS_PAVADINIMAS, RINKINYS.RINKINYS_NUORODA,RINKINYS.RINKINYS_ATNAUJ_DATA,RINKINYS.RINKINYS_IKEL_DATA";


    $sql5 = "SELECT 
organizacija.ORGANIZACIJA_PAVADINIMAS AS Organizacija, 
zyme.ZYME_PAVADINIMAS AS Zyme,
COUNT(buna.ZYME_ID) AS ZymeCount
FROM 
db2.organizacija 
JOIN 
db2.rinkinys ON organizacija.ORGANIZACIJA_ID = rinkinys.ORGANIZACIJA_ID 
LEFT JOIN 
db2.buna ON rinkinys.RINKINYS_ID = buna.RINKINYS_ID 
LEFT JOIN 
db2.zyme ON buna.ZYME_ID = zyme.ZYME_ID
WHERE 
zyme.ZYME_PAVADINIMAS != '0' OR zyme.ZYME_PAVADINIMAS IS NULL
GROUP BY 
organizacija.ORGANIZACIJA_PAVADINIMAS, zyme.ZYME_PAVADINIMAS";

$sql6 = "SELECT db2.organizacija.ORGANIZACIJA_PAVADINIMAS AS Organization,
db2.saugykla.SAUGYKLA_FORMATAS AS Format,
COUNT(*) AS Count
FROM db2.organizacija
LEFT JOIN db2.rinkinys ON db2.organizacija.ORGANIZACIJA_ID = db2.rinkinys.ORGANIZACIJA_ID
LEFT JOIN db2.naudoja ON db2.rinkinys.RINKINYS_ID = db2.naudoja.RINKINYS_ID
LEFT JOIN db2.saugykla ON db2.naudoja.SAUGYKLA_ID = db2.saugykla.SAUGYKLA_ID
GROUP BY db2.organizacija.ORGANIZACIJA_PAVADINIMAS, db2.saugykla.SAUGYKLA_FORMATAS";

    $result1 = $pdo->query($sql1);
    if ($result1->rowCount() > 0) {
      $duom_rink_pav = array();
      $duom_rink_nuorod = array();
      $duom_rink_org_pav = array();
      $duom_rink_perz = array();

      while ($row = $result1->fetch()) {
        $duom_rink_pav[] = $row["RINKINYS_PAVADINIMAS"];
        $duom_rink_nuorod[] = $row["RINKINYS_NUORODA"];
        $duom_rink_org_pav[] = $row["ORGANIZACIJA_PAVADINIMAS"];
        $duom_rink_perz[] = $row["LANKOMUMAS_PERZIUROS"];
      }
      unset($result1);
    } else {
      echo "No records matching your query were found.";
    }
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
      $pavadinimas = array();
      $perziuros = array();
      $organizacija_id = array();


      while ($row = $result->fetch()) {
        
        $pavadinimas[] = $row["ORGANIZACIJA_PAVADINIMAS"];
        $perziuros[] = $row["Total_Perziuros"];
      
      }
      unset($result);
    } else {
      echo "No records matching your query were found.";
    }

    $result3 = $pdo->query($sql3);
    if ($result3->rowCount() > 0) {
      $duom_rink_pav_high = array();
      $duom_rink_nuorod_high = array();
      $duom_rink_org_pav_high = array();
      $duom_rink_atnj_data_high = array();
      $duom_rink_perz_high = array();

      while ($row = $result3->fetch()) {
        $duom_rink_pav_high[] = $row["Rinkinys_Pavadinimas"];
        $duom_rink_nuorod_high[] = $row["Nuoroda"];
        $duom_rink_org_pav_high[] = $row["Organizacija"];
        $duom_rink_atnj_data_high[] = $row["Rinkinys_Atnauj_Data"];
        $duom_rink_perz_high[] = $row["Lankomumas_Perziuros"];
      }
      unset($result3);
    } else {
      echo "No records matching your query were found.";
    }

    $result4 = $pdo->query($sql4);
    if ($result4->rowCount() > 0) {
      $Rinkinys_Tip_Pie = array();
      $Count_Pie = array();

      while ($row = $result4->fetch()) {

        $Rinkinys_Tip_Pie[] = $row["Rinkinys_Tip"];
        $Count_Pie[] = $row["Count"];
      }
      unset($result4);
    } else {
      echo "No records matching your query were found.";
    }
    $result5 = $pdo->query($sql41);
    if ($result5->rowCount() > 0) {
      $Rinkinys_Pav_Pie_add = array();
      $Rinkinys_Nuor_Pie_add = array();
      $Rinkinys_Tip_Pie_add = array();
      $Rinkinys_ikel_data_Pie_add = array();
      $Rinkinys_atn_data_Pie_add = array();

      while ($row = $result5->fetch()) {
        $Rinkinys_Tip_Pie_add[] = $row["Tipas"];
        $Rinkinys_Pav_Pie_add[] = $row["Rinkinys_Pavadinimas"];
        $Rinkinys_Nuor_Pie_add[] = $row["Rinkinys_Nuoroda"];
        $Rinkinys_ikel_data_Pie_add[] = $row["Ikel_Data"];
        $Rinkinys_atn_data_Pie_add[] = $row["Atn_Data"];
      }
      unset($result5);
    } else {
      echo "No records matching your query were found.";
    }
    $result6 = $pdo->query($sql5);
    if ($result6->rowCount() > 0) {
      $Radar_organizacija = array();
      $Radar_zyme = array();
      $Radar_zyme_count = array();

      while ($row = $result6->fetch()) {
        $Radar_organizacija[] = $row["Organizacija"];
        $Radar_zyme[] = $row["Zyme"];
        $Radar_zyme_count[] = $row["ZymeCount"];
      }
      unset($result6);
    } else {
      echo "No records matching your query were found.";
    }
    $result7 = $pdo->query($sql6);
    if ($result7->rowCount() > 0) {
      $PolarArea_organizacija = array();
      $PolarArea_format = array();
      $PolarArea_count = array();

      while ($row = $result7->fetch()) {
        $PolarArea_organizacija[] =$row["Organization"];
        $PolarArea_format[] =$row["Format"];
        $PolarArea_count[] =$row["Count"];
      }
      unset($result7);
    } else {
      echo "No records matching your query were found.";
    }

  } catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
  }

  // Close connection
  unset($pdo);
  ?>

  <div class="background">
    <div class="chartCard"><!-- 1 eile -->
      <div class="chartBox">
        <div class="box_y">
          <div class="sub_box_y">
            <canvas id="myChart"></canvas>
          </div>
        </div>
        <div class="box_x">
          <div class="sub_box_x">
            <canvas id="myChart2"></canvas>
          </div>
        </div>
      </div>
      <div class="chartBox">
        <input type="text" id="searchBar" placeholder="Search...">
        <table id="myTable">
          <thead>
            <tr class="header">
              <th style="width:90%;">Metadomuo</th>
              <th style="width:10%;">Peržiūros</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <!-- Table rows will be dynamically generated here -->
          </tbody>
        </table>
      </div>
    </div>
    <div class="chartCardV2"><!-- 2 eile -->
      <div class="chartBoxV2" id="secondRowV2">
        <canvas id="RadarChart"></canvas>


        <!-- Pie chart will be generated here -->
      </div>
      <div class="chartBoxV2" id="secondRowV2">
        <canvas id="PolarArea"></canvas>


        <!-- Pie chart will be generated here -->
      </div>
    </div>
    <div class="chartCard"><!-- 2 eile -->
      <div class="chartBox" id="secondRow">
        <canvas id="PieChart"></canvas>
        <div>

          <button id="ataskaita_gen" class="custom-button">Generuoti ataskaitą</button>
        </div>
        <!-- Pie chart will be generated here -->
      </div>
    </div>
    <div class="chartCard" style="height: auto;"><!-- 3 eile -->
      <div class="chartBox" id="secondRow">
        <canvas id="LineChart"></canvas>
        <!-- Line chart will be generated here -->
        <div>
          <button id="customButton" class="custom-button">Įkrauti duomenis</button>

        </div>

      </div>

    </div>

    <div class="chartCard"><!-- 3 eile -->
      <div class="chartBox" id="secondRow">

        <div class="menubox">

          <button type="button" id="update_button" class="custom-button">Atnaujinti duomenis</button>
          <p id="loading"></p>


          <button type="button" id="atask_gen_all" class="custom-button">Atsisiųsti visus duomenis</button>
        </div>

        <div>
          <form id="helpdeskForm" method="POST">
            <label for="aprasymas">Atsiliepimas</label>
            <textarea id="aprasymas" name="aprasymas" placeholder="Rašyti čia" style="height:200px;"></textarea>

            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            <input type="button" value="Issiusti" onclick="submitForm()">
          </form>

          <p id="submittedText">Jūsų atsiliepimas buvo išsiųstas</p>
        </div>
      </div>
    </div>
    <a>Metaduomenys yra renkami iš: </a>
    <a href="https://data.gov.lt" target="_blank">Lietuvos atvirų duomenų portalas</a>
  </div>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>

    document.getElementById('update_button').addEventListener('click', function () {
      // Send a request to a PHP script to trigger the execution of the .exe file
      var h1Element = document.getElementById("loading");
      h1Element.innerText = "Atnaujinama...";
      var xhr = new XMLHttpRequest();

      xhr.addEventListener('load', function () {
        // This function will execute when the request is completed successfully
        h1Element.innerText = "Atnaujinimas baigtas!";
        location.reload();

      });

      xhr.open('GET', 'execute_exe.php', true);
      xhr.send();
    });

    document.getElementById('atask_gen_all').addEventListener('click', function () {

      var xhr = new XMLHttpRequest();

      xhr.addEventListener('load', function () {
        // This function will execute when the request is completed successfully
        console.log("Done")
        var fileUrl = '/output.xlsx'; // URL of the file to download

        // Create a temporary link element
        var link = document.createElement('a');
        link.href = fileUrl;
        link.download = 'output.xlsx'; // Specify the filename

        // Append the link to the body
        document.body.appendChild(link);

        // Trigger the click event to start the download
        link.click();

        // Clean up
        document.body.removeChild(link);

      });

      xhr.open('GET', 'vis_duom.php', true);
      xhr.send();
    });


    function submitForm() {
      var form = document.getElementById("helpdeskForm");
      var textareaValue = document.getElementById("aprasymas").value.trim(); // Get textarea value and trim whitespace

      // Execute reCAPTCHA and obtain a token
      grecaptcha.ready(function () {
        grecaptcha.execute('6LdsR84pAAAAAOSyV5zT4LzrZPldUWdysiAYVrK3', { action: 'submit' }).then(function (token) {
          // Set the token value to the hidden input field
          document.getElementById('g-recaptcha-response').value = token;

          // Check if textarea is empty
          if (textareaValue === "") {
            // Show error message
            document.getElementById('submittedText').innerText = 'Jūs neužpildėte pranešimo';
            document.getElementById('submittedText').style.color = 'red';
            return; // Exit function if textarea is empty
          }
          else {
            document.getElementById('submittedText').innerText = 'Pranešimas išsiųstas';
            document.getElementById('submittedText').style.color = 'green';
          }

          // Get form data
          var formData = new FormData(form);

          // Create an XMLHttpRequest object
          var xhr = new XMLHttpRequest();

          // Define what happens on successful data submission
          xhr.onload = function () {
            if (xhr.status === 200) {
              // Update the UI to indicate successful submission
              document.getElementById('submittedText').style.display = 'block';
            } else {
              // Update the UI to indicate an error
              document.getElementById('submittedText').innerText = 'Error submitting form.';
              document.getElementById('submittedText').style.color = 'red';
            }
          };

          // Define what happens in case of an error
          xhr.onerror = function () {
            // Update the UI to indicate an error
            document.getElementById('submittedText').innerText = 'Error submitting form.';
            document.getElementById('submittedText').style.color = 'red';
          };

          // Set up the request
          xhr.open('POST', '/send_to_db.php', true);

          // Send the form data with the reCAPTCHA token
          form.reset();
          xhr.send(formData);
        });
      });
    }

    // Function to generate random color
    function getRandomColor() {
      const letters = "0123456789ABCDEF";
      let color = "#";
      for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    }

    //horizontal bar chart
    const pavadinimas = <?php echo json_encode($pavadinimas); ?>;
    const perziuros = <?php echo json_encode($perziuros); ?>;
    //vertical bar chart
    const duom_rink_pav = <?php echo json_encode($duom_rink_pav); ?>;
    const duom_rink_nuorod = <?php echo json_encode($duom_rink_nuorod); ?>;
    const duom_rink_org_pav = <?php echo json_encode($duom_rink_org_pav); ?>;
    const duom_rink_perz = <?php echo json_encode($duom_rink_perz); ?>;

    //List
    const duom_rink_pav_high = <?php echo json_encode($duom_rink_pav_high); ?>;
    const duom_rink_nuorod_high = <?php echo json_encode($duom_rink_nuorod_high); ?>;
    const duom_rink_org_pav_high = <?php echo json_encode($duom_rink_org_pav_high); ?>;
    const duom_rink_atnj_data_high = <?php echo json_encode($duom_rink_atnj_data_high); ?>;
    const duom_rink_perz_high = <?php echo json_encode($duom_rink_perz_high); ?>;

    //Piechart
    const Rinkinys_Tip_Pie = <?php echo json_encode($Rinkinys_Tip_Pie); ?>;
    const Count_Pie = <?php echo json_encode($Count_Pie); ?>;

    //Piechart_Add

    const Rinkinys_Pav_Pie_add = <?php echo json_encode($Rinkinys_Pav_Pie_add); ?>;
    const Rinkinys_Nuor_Pie_add = <?php echo json_encode($Rinkinys_Nuor_Pie_add); ?>;
    const Rinkinys_Tip_Pie_add = <?php echo json_encode($Rinkinys_Tip_Pie_add); ?>;
    const Rinkinys_atn_data_Pie_add = <?php echo json_encode($Rinkinys_atn_data_Pie_add); ?>;
    const Rinkinys_ikel_data_Pie_add = <?php echo json_encode($Rinkinys_ikel_data_Pie_add); ?>;

    //radar
    const Radar_organizacija = <?php echo json_encode($Radar_organizacija); ?>;
    const Radar_zyme = <?php echo json_encode($Radar_zyme); ?>;
    const Radar_zyme_count = <?php echo json_encode($Radar_zyme_count); ?>;

    //PolarArea

    const PolarArea_organizacija =<?php echo json_encode($PolarArea_organizacija); ?>;
    const PolarArea_format =<?php echo json_encode($PolarArea_format); ?>;
    const PolarArea_count =<?php echo json_encode($PolarArea_count); ?>;
    //console.log(Rinkinys_Tip_Pie_add)
    let radar_data = []
    
    for (let i = 0; i < Radar_organizacija.length; i++) {
      let rowData = {
        Radar_organizacija: Radar_organizacija[i],
        Radar_zyme: Radar_zyme[i],
        Radar_zyme_count: Radar_zyme_count[i]
      };
      radar_data.push(rowData);
    }
    //polar_area_data
    let polar_area_data = []
    
    for (let i = 0; i < PolarArea_organizacija.length; i++) {
      let rowData = {
        PolarArea_Organizacija: PolarArea_organizacija[i],
        PolarArea_Format: PolarArea_format[i],
        PolarArea_Count: PolarArea_count[i]
      };
      polar_area_data.push(rowData);
    }
    console.log(polar_area_data)
    //#console.log(radar_data)
    let db_paieska_table = [];
    // Loop through the arrays and construct objects
    for (let i = 0; i < duom_rink_pav_high.length; i++) {
      let rowData = {
        pavadinimas: duom_rink_pav_high[i],
        nuoroda: duom_rink_nuorod_high[i],
        organizacija: duom_rink_org_pav_high[i],
        data: duom_rink_atnj_data_high[i],
        perziuros: duom_rink_perz_high[i]
      };

      // Push the object into the db_paieska_table array
      db_paieska_table.push(rowData);
    }

    let db_paieska_data_structure = [];
    for (let i = 0; i < pavadinimas.length; i++) {
      const label = pavadinimas[i];
      const matchingRinkiniai = [];

      // Iterate through duom_rink_pav to find matching elements
      for (let j = 0; j < duom_rink_pav.length; j++) {
        if (label == duom_rink_org_pav[j]) {
          matchingRinkiniai.push({
            x: duom_rink_pav[j],
            y: duom_rink_perz[j],
            nuoroda: duom_rink_nuorod[j]

          });
        }
      }

      // Push an object with x, y, and rinkiniai properties into db_paieska_data_structure
      db_paieska_data_structure.push({
        y: label,
        x: perziuros[i],
        rinkiniai: matchingRinkiniai
      });
    }

    //piechartttt

    let piechart_data_add = [];
    for (let i = 0; i < Rinkinys_Tip_Pie.length; i++) {
      const label1 = Rinkinys_Tip_Pie[i]; // Use Rinkinys_Tip_Pie_add here
      const matchingPieData = [];

      // Iterate through Rinkinys_Pav_Pie_add to find matching elements
      for (let j = 0; j < Rinkinys_Tip_Pie_add.length; j++) {
        if (label1 == Rinkinys_Tip_Pie_add[j]) {
          matchingPieData.push({
            rink_pav: Rinkinys_Pav_Pie_add[j],
            rink_nuor: Rinkinys_Nuor_Pie_add[j],
            rink_tip: Rinkinys_Tip_Pie_add[j],
            rink_view: duom_rink_perz_high[j],
            rink_ikel_data: Rinkinys_ikel_data_Pie_add[j],
            rink_atn_data: Rinkinys_atn_data_Pie_add[j]
          });
        }
      }
      // Push an object with x, y, and rinkiniai properties into piechart_data_add array
      piechart_data_add.push({
        type: label1,
        count: Count_Pie[i],
        rinkiniai: matchingPieData
      });
    }
    //console.log(piechart_data_add);
    /////////bar chart y
    const data = {
      labels: pavadinimas,
      datasets: [{
        label: db_paieska_data_structure.rinkiniai,
        data: db_paieska_data_structure,
        backgroundColor: [
          'rgba(255, 26, 104, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)',
          'rgba(0, 0, 0, 0.2)'
        ],
        borderColor: [
          'rgba(255, 26, 104, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)',
          'rgba(0, 0, 0, 1)'
        ],
        borderWidth: 1
      }]
    };
    //config
    // config 
    const config = {
      type: 'bar',
      data: data,
      options: {
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
          tooltip: {
            callbacks: {
              /*beforeTitle: function (context) {
                const index = context[0].dataIndex;
                const labelText = `Data at index ${index}: ${pavadinimas[index]}`;
                return labelText;
              },
              title: function (context) {
                return ''
              }*/
            }
          },
          legend: {
            display: false
          },
          title: {
            display: true,
            text: 'Įmonės',
            font: {
              size: 16
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true
          },
          x: {
            ticks: {
              display: false
            }
          }
        }
      },
    };
    //render
    // render init block

    const ctx = document.getElementById('myChart')

    const myChart = new Chart(
      ctx,
      config
    );


    //////////Bar chart x
    const data2 = {
      labels: "",
      datasets: [{
        label: "",
        data: "",
        backgroundColor: [
          'rgba(255, 26, 104, 0.2)'
        ],
        borderColor: [
          'rgba(255, 26, 104, 1)'
        ],
        borderWidth: 1
      }]
    };
    //config
    // config 

    const config2 = {
      type: 'bar',
      data: data2,
      options: {
        maintainAspectRatio: false,

        plugins: {
          legend: {
            display: false
          },
          title: {
            responsive: false,
            display: true,
            text: 'Duomenų rinkiniai',
            font: {
              size: 16
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true
          },
          x: {
            ticks: {
              display: false
            }
          },
        },
      }
    };
    //render
    // render init block
    const ctx2 = document.getElementById('myChart2')
    const myChart2 = new Chart(
      ctx2,
      config2
    );

    ///////////pie chart 1
    const dataPie = {
      labels: Rinkinys_Tip_Pie,
      datasets: [{
        label: 'Duomenų rinkinių kiekis',
        data: piechart_data_add,
        backgroundColor: [
          'rgb(255, 99, 132)',
          'rgb(54, 162, 235)',
          'rgb(255, 205, 86)'
        ],
      }]
    };
    const configPie = {
      type: 'doughnut',
      data: dataPie,
      options: {
        parsing: {
          key: 'count'
        },
        maintainAspectRatio: false,
        plugins: {
          title: {
            responsive: false,
            display: true,
            text: 'Duomenų kategorijos',
            font: {
              size: 16
            }
          },
          legend: {
            display: true,
            position: 'left',
            maxWidth: 685, // Adjust the maxWidth as needed
            onClick: function (e, legendItem) {
              var ci = this.chart;
              var meta = ci.getDatasetMeta(0); // Assuming only one dataset

              var selectedIndexes = [];
              var allHidden = true;

              // Determine selected indexes and current visibility state
              meta.data.forEach(function (data, i) {
                if (data.hidden) {
                  allHidden = false;
                } else {
                  selectedIndexes.push(i);
                }
              });

              var index = legendItem.index;

              // Toggle visibility based on selection
              if (allHidden || selectedIndexes.length > 10) {
                // Show only the clicked dataset
                meta.data.forEach(function (data, i) {
                  data.hidden = i !== index;
                });
              } else if (selectedIndexes.includes(index)) {
                // Hide the clicked dataset if it's already selected
                meta.data[index].hidden = true;
              } else {
                // Show the clicked dataset
                meta.data[index].hidden = false;
              }

              ci.update(); // Update the chart
            }
          }
        },

      }
    };

    const elementPie = document.getElementById('PieChart')
    const PieChart = new Chart(
      elementPie,
      configPie
    );
    const LineData = {
      //labels: "",
      datasets: [{
        label: "Peržiūros",
        data: "",
        imgDataUrls: "",
        fill: false,
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1,
      }]
    };
    const LineConfig = {
      type: 'line',
      data: LineData,
      options: {
        plugins: {
          tooltip: {
            enabled: true,
            mode: 'single',
            callbacks: {
              label: function (tooltipItems, data) {
                var text = tooltipItems.datasetIndex === 0 ? 'some text 1' : 'some text 2'
                return tooltipItems.yLabel + ' ' + text;
              },
              title: function (context) {
                return ''
              }
              /* beforeTitle: function (context) {
               const index = context[0].dataIndex;
               const labelText = `Data at index ${index}: ${pavadinimas[index]}`;
               return labelText;
             },*/
              /*title: function(context){
              return ''
            },*/
            }
          },
        },
        maintainAspectRatio: false,
        plugins: {
          /*datalabels:{
            color: 'blue',
            labels: 100,
          },*/
          legend: {
            display: false,
          }
        },
        scales: {
          y: {
            display: true, // Hide labels on the y-axis
          },
          x: {
            display: false, // Hide labels on the x-axis
          }
        }
      },
      // plugins: [ChartDataLabels]   
    };
    const elementLine = document.getElementById('LineChart')
    const LineChart = new Chart(
      elementLine,
      LineConfig
    );
    //radar radar_data
    const dataRadar = {
      labels: ['', '', '', '', ''],
      datasets: [{
        label: 'Rinkinys',
        data: [0, 0, 0, 0, 0],
        fill: true,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgb(255, 99, 132)',
        pointBackgroundColor: 'rgb(255, 99, 132)',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: 'rgb(255, 99, 132)'
      }]
    };
    const RadarConfig = {
      type: 'radar',
      data: dataRadar,
      options: {
        plugins: {
          title: {
            display: true,
            text: 'Duomenų žymės',
            font: {
              size: 16
            }
          }
        },
        scales: {
          r: {
            angleLines: {
              display: true
            },
            Min: 5,
            Max: 5
          },
        },
        maintainAspectRatio: false,
        elements: {
          line: {
            borderWidth: 3
          }
        }
      },
    };
    const elementRadar = document.getElementById('RadarChart')
    const RadarChart = new Chart(
      elementRadar,
      RadarConfig
    );

    const dataPolarArea = {
      labels: [
    'Rinkinys',
    ],
    datasets: [{
      label: 'My First Dataset',
      data: [11],
      backgroundColor: [
        'rgb(255, 99, 132)',
      ]
  }]
    };
    const PolarAreaConfig = {
      type: 'polarArea',
      data: dataPolarArea,
      options: {
        maintainAspectRatio: false,
        elements: {
          line: {
            borderWidth: 3
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'Duomenų formatai',
            font: {
              size: 16
            }
          }
        },
      },
    };
    const elementPolarArea = document.getElementById('PolarArea')
    const PolarArea = new Chart(
      elementPolarArea,
      PolarAreaConfig
    );


    //radar_data
    function clickHandler(click) {
      const points = myChart.getElementsAtEventForMode(click, 'nearest', { intersect: true }, true)
      if (points.length) {
        const organizacijos_pavad = points[0].element.$context.raw.y;
        console.log(points[0].element.$context.raw);

        // Filter radar_data based on organizacijos_pavad
        const filteredData = radar_data
          .filter((value) => value.Radar_organizacija === organizacijos_pavad)
          .map(({ Radar_zyme, Radar_zyme_count }) => ({ Radar_zyme, Radar_zyme_count }));

        //console.log(filteredData);

        const kitaFilterData = []
        let sum = 0
        for (let i = 0; i < filteredData.length; i++) {
          if (filteredData[i].Radar_zyme_count <= 2) {
            //let integr = parseInt(filteredData[i].Radar_zyme_count)
            let itemdelete = filteredData[i].Radar_zyme
            sum = Number(sum) + Number(filteredData[i].Radar_zyme_count)
            //var filtered = filteredData.filter(function(el) { return el.Radar_zyme == itemdelete; }); 
            //filteredData.splice(i, 1);
            //delete filteredData[i];

            //console.log(filtered)
            //console.log("Integer", integr)
            // console.log("Maziau uz du rasta:", sum)
          }
        }
        filteredData.push({
          Radar_zyme: 'kita',
          Radar_zyme_count: sum.toString(),
        });
        let finalData = filteredData
          .filter((value) => value.Radar_zyme_count > 2)
          .map(({ Radar_zyme, Radar_zyme_count }) => ({ Radar_zyme, Radar_zyme_count }));


        RadarChart.data.datasets[0].label = organizacijos_pavad
        RadarChart.data.labels = finalData.map(item => item.Radar_zyme);
        RadarChart.data.datasets[0].data = finalData.map(item => item.Radar_zyme_count);
        RadarChart.update();
        //console.log(points.length)
        //console.log(points[0].element.$context.raw.rinkiniai)
        //console.log(points[0].element.$context.raw)
        //console/log(pavadinimas[0])
        myChart2.config.data.datasets[0].data = points[0].element.$context.raw.rinkiniai;
        //rikiavimo duomenys//rikiavimo_duom = points[0].element.$context.raw.rinkiniai;
        //myChart2.config.data.datasets[0].label = points[0].element.$context.raw.rinkiniai.nuoroda;
        myChart2.update();

        const AreafilteredData = polar_area_data
    .filter((value) => value.PolarArea_Organizacija === organizacijos_pavad)
    .map(({ PolarArea_Format, PolarArea_Count }) => ({
        PolarArea_Format: PolarArea_Format === "0" ? "Nepaminėta" : PolarArea_Format,
        PolarArea_Count
    }));
        PolarArea.data.datasets[0].label = organizacijos_pavad
        PolarArea.data.labels = AreafilteredData.map(item => item.PolarArea_Format);
        PolarArea.data.datasets[0].data = AreafilteredData.map(item => item.PolarArea_Count);
        PolarArea.update();

      }
    };
    function clickHandlerLinkOpen(click) {
      const points = myChart2.getElementsAtEventForMode(click, 'nearest', { intersect: true }, true)
      if (points.length) {
        //console.log(points.length)
        const link = points[0].element.$context.raw.nuoroda
        window.open(link, '_blank');
      }
    };
    function clickPieData(click) {
      const points = PieChart.getElementsAtEventForMode(click, 'nearest', { intersect: true }, true);

      if (points.length) {
        //console.log(points[0].element.$context.raw.rinkiniai);
        const selectedData = points[0].element.$context.raw.rinkiniai;
        //const selectedData2 = points[0].element.$context.raw.type;
        //console.log(points[0].element.$context.raw)
        //const names = selectedData2.map(entry => entry.type);
        // Extracting data and labels from the selected data
        const data = selectedData.map(entry => entry.rink_view);
        const labels = selectedData.map(entry => entry.rink_pav);
        const imgDataUrls = selectedData.map(entry => entry.rink_nuor);
        // const tipas = 

        // Ensure LineChart object is initialized with an empty datasets array
        if (!LineChart.data.datasets) {
          LineChart.data.datasets = [];
        }

        // Generate random color for each dataset
        const randomColor = getRandomColor();

        // Update the LineChart data and labels


        LineChart.data.labels = labels;
        LineChart.data.datasets = [{
          //label: names,
          data: data,
          imgDataUrls: imgDataUrls,
          backgroundColor: randomColor, // Apply the random color to the dataset background
          borderColor: randomColor,     // Apply the same color to the dataset border
          borderWidth: 1,
          labels: labels,     // You can adjust the border width if needed
        }];

        LineChart.options.plugins.tooltip.callbacks.label = function (tooltipItems, data) {
          console.log(tooltipItems)
          return tooltipItems.dataset.labels[tooltipItems.dataIndex];
        }
        LineChart.options.plugins.tooltip.callbacks.title = function (context) {
          return "";
        }
        /*LineChart.options.plugins.tooltip.callbacks.title = function(context) {
          return '';
        };*/

        /*LineChart.options.plugins.tooltip.callbacks.beforeTitle = function (context) {
                    const index = context[0].dataIndex;
                    const labelText = `Data at index ${index}: ${labels[index]}`;
                    return labelText;
                  },*/

        //console.log(LineChart)
        LineChart.update();
      }
    }


    function getRandomColor() {
      const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16);
      return randomColor;
    }

    function clickLineLink(click) {
      const points = LineChart.getElementsAtEventForMode(click, 'nearest', { intersect: true }, true);
      if (points.length) {
        const indexx = points[0].index
        const datasetIndex = points[0].datasetIndex;
        //console.log(points[0].index);
        //console.log(LineChart.config.data);
        //console.log(datasetIndex)
        // Extract the link from the clicked data point
        const link = LineChart.config.data.datasets[datasetIndex].imgDataUrls[indexx];
        if (link) {
          window.open(link, '_blank'); // Open the link in a new tab
        } else {
          console.log("No link available for this data point.");
        }
      }
    };

    document.getElementById("ataskaita_gen").addEventListener("click", function () {

      const data = [];
      PieChart.data.datasets[0].data.forEach((value, index) => {
        const meta = PieChart.getDatasetMeta(0);
        if (!meta.data[index].hidden) {
          data.push(value);
        }
      });
      console.log(data)
      // Function to convert data to Excel format
      function convertToExcel(data) {
        const workbook = XLSX.utils.book_new();
        data.forEach(category => {
          const worksheetData = [];
          worksheetData.push(["Tipas", "Pavadinimas", "Nuoroda", "Peržiūrų skaičius", "Įkėlimo data", "Atnaujinimo data"]);
          category.rinkiniai.forEach(item => {
            worksheetData.push([category.type, item.rink_pav, item.rink_nuor, item.rink_view, item.rink_ikel_data, item.rink_atn_data]);
          });
          const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);
          XLSX.utils.book_append_sheet(workbook, worksheet);
        });
        return workbook;
      }

      // Function to download the Excel file
      function downloadExcel(workbook, filename) {
        XLSX.writeFile(workbook, filename);
      }

      // Convert data to Excel format
      const excelData = convertToExcel(data);

      // Download the Excel file
      downloadExcel(excelData, 'example.xlsx');
    });

    //////keliu datasets ikelimas
    document.getElementById("customButton").addEventListener("click", function () {
      // Extract data from the pie chart that is visible
      LineChart.data.datasets = [];
      const pieData = [];
      PieChart.data.datasets[0].data.forEach((value, index) => {
        const meta = PieChart.getDatasetMeta(0);
        if (!meta.data[index].hidden) {
          pieData.push(value);
        }
      });
      let label_length = 0;
      let labelis_v2 = [];
      //console.log(pieData);
      pieData.forEach((value, index) => {
        //console.log(value)
        //console.log(value.rinkiniai)
        const selected_data = value.rinkiniai;

        const tipas = value.type;
        const data = selected_data.map(entry => entry.rink_view);
        const labels = selected_data.map(entry => entry.rink_pav);
        const labels_lab = selected_data.map(entry => entry.rink_pav);
        const imgDataUrls = selected_data.map(entry => entry.rink_nuor);
        //console.log(data);
        if (data.length > label_length) {
          label_length = data.length;
          labelis_v2 = labels;
          //console.log(data.length, labels);
        }
        const randomColor = getRandomColor();
        const newDataset = {
          label: tipas, // You can customize the label as needed
          imgDataUrls: imgDataUrls,
          backgroundColor: 'rgba(255, 99, 132, 0.2)', // Customize background color
          borderColor: randomColor,     // Apply the same color to the dataset border
          borderWidth: 2,
          data: data,
          labels: labels,
        };
        //labelis_v2.push(labels);
        LineChart.data.labels = labelis_v2;
        // newDataset.labels = labels;
        //LineChart.data.labels = labelis_v2;
        LineChart.options.plugins.tooltip.callbacks.label = function (tooltipItems, data) {
          //console.log(tooltipItems)
          return tooltipItems.dataset.label + ': ' + tooltipItems.dataset.labels[tooltipItems.dataIndex];
        }
        LineChart.options.plugins.tooltip.callbacks.title = function (context) {
          return "";
        }

        //console.log(LineChart.data.datasets)
        LineChart.data.datasets.push(newDataset);

      });

      LineChart.update();
    });

    ctx.onclick = clickHandler;
    ctx2.onclick = clickHandlerLinkOpen;
    elementPie.onclick = clickPieData;
    elementLine.onclick = clickLineLink;



    //Scroll

    const sub_box_y = document.querySelector('.sub_box_y');
    if (myChart.data.labels.length > 7) {
      const newHeight = 500 + ((myChart.data.labels.length - 7) * 20);
      sub_box_y.style.height = `${newHeight}px`;
    };
    const sub_box_x = document.querySelector('.sub_box_x');
    if (myChart2.data.labels.length > 7) {
      //const newWidth = 700 + ((myChart2.data.labels.length - 7) * 30);
      sub_box_x.style.width = `800px`;
      //sub_box_x.style.width = `${newWidth}px`;
    };


    //LENTELES PILDYMAS IR PAIESKA SKRIPTAS
    generateTableRows();
    searchBar.addEventListener("input", filterTable);
    // padaryti radar, kai paspaudi ant bar , ismeta organizacijas ir jos zymas, pagal zymu counta yra ziurima vertes
  </script>

</body>

</html>