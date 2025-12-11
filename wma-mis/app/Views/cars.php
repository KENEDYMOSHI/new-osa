<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vehicle Form</title>
  <!-- Bootstrap 5 CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Vehicle Information</h4>
          </div>
          <div class="card-body">
            <form id="vehicleForm" class="needs-validation">
              <div class="mb-3">
                <label for="name" class="form-label">Vehicle Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  name="name" />
                <div class="invalid-feedback">
                  Please provide a vehicle name.
                </div>
              </div>

              <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <select class="form-select" id="brand" name="brand">
                  <option value="" selected disabled>Select Brand</option>
                  <option value="Toyota">Toyota</option>
                  <option value="Honda">Honda</option>
                  <option value="Ford">Ford</option>
                  <option value="BMW">BMW</option>
                </select>
                <div class="invalid-feedback">Please select a brand.</div>
              </div>

              <div class="mb-3">
                <label for="makeYear" class="form-label">Make Year</label>
                <input
                  type="number"
                  class="form-control"
                  id="makeYear"
                  name="makeYear"
                  min="1900"
                  max="2023" />
                <div class="invalid-feedback">
                  Please provide a valid year between 1900 and 2023.
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label">Fuel Type</label>
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="fuelType"
                    id="petrol"
                    value="petrol" />
                  <label class="form-check-label" for="petrol">Petrol</label>
                </div>
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="fuelType"
                    id="diesel"
                    value="diesel" />
                  <label class="form-check-label" for="diesel">Diesel</label>
                </div>
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="fuelType"
                    id="electric"
                    value="electric" />
                  <label class="form-check-label" for="electric">Electric</label>
                </div>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>

            <div
              id="responseMessage"
              class="mt-3 alert"
              style="display: none"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="<?= base_url('assets/js/FormEngine.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- let url = "http://localhost:8000/addCar"; -->
  <script>
    let url = "<?= base_url() ?>addCar";
    // Initialize the form
    const form = new FormEngine('vehicleForm', url);

    // Optional: Customize success/error handlers
    form.onSuccess = function(data) {
      // Handle successful submission
      console.log('Form submitted successfully!', data);


      swal({
        title: "Success",
        text: data.msg,
        icon: "success",
      });

      // You could redirect or reset the form:
      // window.location.href = '/success-page';
      // this.form.reset();
    };

    form.onError = function(data) {
      // Handle general errors (specific errors are shown automatically)
      console.error('Form error:', data);
    };

    form.beforeSubmit = function(data) {
      data.append('city', 'Tokyo')
    };
  </script>
</body>

</html>