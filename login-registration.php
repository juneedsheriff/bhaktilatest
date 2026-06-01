<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="loginForm">
          <div class="mb-3">
            <label>Email / Username</label>
            <input type="text" class="form-control" name="email" required>
          </div>

          <div class="mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>

          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
      </div>

      <div class="text-center mt-2 mb-3">
        <small>Don't have an account? 
          <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Register</a>
        </small>
      </div>
    </div>
  </div>
</div>


<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Register</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="registerForm">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>First Name</label>
              <input type="text" class="form-control" name="first_name" required>
            </div>

            <div class="col-md-6 mb-3">
              <label>Last Name</label>
              <input type="text" class="form-control" name="last_name" required>
            </div>
          </div>

          <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>

          <div class="mb-3">
            <label>Mobile</label>
            <input type="text" class="form-control" name="mobile" required>
          </div>

          <div class="mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>

          <button type="submit" class="btn btn-success w-100">Register</button>
        </form>
      </div>

      <div class="text-center mt-2 mb-3">
        <small>Already have an account? 
          <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login</a>
        </small>
      </div>
    </div>
  </div>
</div>


