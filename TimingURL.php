<?php
// bootstrap_php_webform.php
// Single-file PHP + Bootstrap webform that handles submission and server-side validation.
// Save this file to a PHP-capable server (e.g. Apache with PHP) and open it in your browser.

// Helper: sanitize
function clean($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

$errors = [];
$submitted = false;
$values = [
    'block' => '',
    'refresh' => '',
    'number' => '',
    'primaryid' => '',
    'secondaryid' => '',
    'location' => '',
    'maxdeviation' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;

    // Block (optional integer)
    if (isset($_POST['block']) && $_POST['block'] !== '') {
      if (filter_var($_POST['block'], FILTER_VALIDATE_INT) === false) {
        $errors[] = 'Block must be an integer.';
      } else {
        $values['block'] = (int)$_POST['block'];
      }
    }

    // Refresh (integer)
    if (isset($_POST['refresh']) && $_POST['refresh'] !== '') {
        // allow integers only
        if (filter_var($_POST['refresh'], FILTER_VALIDATE_INT) === false) {
            $errors[] = 'Refresh must be an integer.';
        } else {
            $values['refresh'] = (int)$_POST['refresh'];
        }
    } else {
        $errors[] = 'Refresh is required.';
    }

    // Number (integer)
    if (isset($_POST['number']) && $_POST['number'] !== '') {
        // allow integers only
        if (filter_var($_POST['number'], FILTER_VALIDATE_INT) === false) {
            $errors[] = 'Number must be an integer.';
        } else {
            $values['number'] = (int)$_POST['number'];
        }
    } else {
        $errors[] = 'Number is required.';
    }

    // PrimaryID
    if (isset($_POST['primaryid']) && $_POST['primaryid'] !== '') {
        $values['primaryid'] = clean($_POST['primaryid']);
        if (mb_strlen($values['primaryid']) > 100) $errors[] = 'PrimaryID is too long (max 100 chars).';
    } else {
        $errors[] = 'PrimaryID is required.';
    }

    // SecondaryID (optional)
    if (isset($_POST['secondaryid']) && $_POST['secondaryid'] !== '') {
        $values['secondaryid'] = clean($_POST['secondaryid']);
        if (mb_strlen($values['secondaryid']) > 100) $errors[] = 'SecondaryID is too long (max 100 chars).';
    }

    // Location: only "Start" or "Finish"
    $allowedLocations = ['Start', 'Finish'];
    if (isset($_POST['location']) && in_array($_POST['location'], $allowedLocations, true)) {
        $values['location'] = $_POST['location'];
    } else {
        $errors[] = 'Location must be either "Start" or "Finish".';
    }

    // MaxDeviation: allow float (can be negative or positive)
    if (isset($_POST['maxdeviation']) && $_POST['maxdeviation'] !== '') {
        $md = str_replace(',', '.', $_POST['maxdeviation']); // allow comma decimal
        if (!is_numeric($md)) {
            $errors[] = 'MaxDeviation must be a number.';
        } else {
            $values['maxdeviation'] = (float)$md;
        }
    } else {
        $errors[] = 'MaxDeviation is required.';
    }

}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RaceClocker timing URL generator</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-3">Fill in form below to generate URL</h3>

          <?php if ($submitted && !empty($errors)): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                  <li><?php echo clean($e); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php elseif ($submitted): ?>
            <div class="alert alert-success">
          <?php if ($submitted && empty($errors)): ?>
            <h5>Timing checker URL:</h5>
            <?php
              $host = $_SERVER['HTTP_HOST'];
              $dir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
              $URL = 'https://'.$host . $dir . '/';
              if ($values['block'] <> "") {
                $URL = $URL . "?Block=".clean((string)$values['block'])."&Refresh=".clean((string)$values['refresh']);
              } else {
                $URL = $URL . "?Refresh=".clean((string)$values['refresh']);
              }
              $URL = $URL . "&Number=".$values['number']."&PrimaryID=".$values['primaryid']."&SecondaryID=".$values['secondaryid']."&Location=".clean($values['location'])."&MaxDeviation=".clean((string)$values['maxdeviation']);
              echo "<a href='".$URL . "' target='_blank'>".$URL."</a>";
              ?>

          <?php endif; ?>
            </div>
          <?php endif; ?>

          <form method="post" novalidate>

            <div class="mb-3">
              <label for="block" class="form-label">Block (optional)</label>
              <input type="number\" class="form-control" id="block" name="block" value="<?php echo isset($values['block']) ? $values['block'] : ''; ?>">
            </div>

            <div class="mb-3">
              <label for="refresh" class="form-label">Refresh</label>
              <input type="number" class="form-control" id="refresh" name="refresh" value="<?php echo isset($values['refresh']) ? $values['refresh'] : ''; ?>" required>
              <div class="form-text">Enter an integer value.</div>
            </div>

            <div class="mb-3">
              <label for="number" class="form-label">Number</label>
              <input type="number" class="form-control" id="number" name="number" value="<?php echo isset($values['number']) ? $values['number'] : ''; ?>" required>
              <div class="form-text">Enter an integer value.</div>
            </div>

            <div class="mb-3">
              <label for="primaryid" class="form-label">PrimaryID</label>
              <input type="text" class="form-control" id="primaryid" name="primaryid" maxlength="100" value="<?php echo $values['primaryid']; ?>" required>
            </div>

            <div class="mb-3">
              <label for="secondaryid" class="form-label">SecondaryID</label>
              <input type="text" class="form-control" id="secondaryid" name="secondaryid" maxlength="100" value="<?php echo $values['secondaryid']; ?>">
            </div>

            <div class="mb-3">
              <label for="location" class="form-label">Location</label>
              <select class="form-select" id="location" name="location" required>
                <option value="" disabled <?php echo $values['location'] === '' ? 'selected' : ''; ?>>Choose...</option>
                <option value="Start" <?php echo $values['location'] === 'Start' ? 'selected' : ''; ?>>Start</option>
                <option value="Finish" <?php echo $values['location'] === 'Finish' ? 'selected' : ''; ?>>Finish</option>
              </select>
              <div class="form-text">Allowed values: Start or Finish.</div>
            </div>

            <div class="mb-3">
              <label for="maxdeviation" class="form-label">MaxDeviation</label>
              <input type="number" step="any" class="form-control" id="maxdeviation" name="maxdeviation" value="<?php echo $values['maxdeviation']; ?>" required>
              <div class="form-text">A numeric value (decimals allowed).</div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Generate</button>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Optional: Bootstrap bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
