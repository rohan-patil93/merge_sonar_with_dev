<!DOCTYPE html>
<html>
<head>
  <title>Login with Google</title>
 <!--  <script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-auth.js"></script> -->
  <script src="https://www.gstatic.com/firebasejs/10.8.1/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.8.1/firebase-auth-compat.js"></script>

</head>
<body>
  <h2>Login with Google</h2>
  <button onclick="loginWithGoogle()">Sign In</button>

  <script>
    // Replace with your Firebase project config
    const firebaseConfig = {
      apiKey: "AIzaSyDHbrNHCoACpSBqlLzbDtSAT5eeTiKop4I",
      authDomain: "infistaq.firebaseapp.com",
      projectId: "infistaq",
    };

    firebase.initializeApp(firebaseConfig);

    function loginWithGoogle() {
      const provider = new firebase.auth.GoogleAuthProvider();

      firebase.auth().signInWithPopup(provider)
        .then(async (result) => {
          const token = await result.user.getIdToken();
          console.log("ID Token:", token);

          // Send the token to PHP server
          fetch("backend.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "idToken=" + encodeURIComponent(token),
          })
          .then((res) => res.text())
          .then((data) => alert("Server response: " + data));
        })
        .catch((error) => {
          console.error("Login error", error);
        });
    }
  </script>
</body>
</html>
