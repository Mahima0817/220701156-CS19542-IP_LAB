import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

@WebServlet("/register")
public class RegisterServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;

    // Database credentials
    private static final String DB_URL = "jdbc:mysql://localhost:3306/UserDB";
    private static final String DB_USER = "root";
    private static final String DB_PASSWORD = "password";

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        response.setContentType("text/html");
        PrintWriter out = response.getWriter();

        // Get parameters
        String username = request.getParameter("username");
        String email = request.getParameter("email");
        String password = request.getParameter("password");

        // Validation flags
        boolean isValid = true;
        StringBuilder errorMessage = new StringBuilder();

        // Username validation
        if (username == null || username.length() < 3 || username.length() > 12) {
            errorMessage.append("Username must be between 3 and 12 characters.<br>");
            isValid = false;
        }

        // Email validation using regex
        String emailRegex = "^[A-Za-z0-9+_.-]+@[A-Za-z0-9.-]+$";
        if (email == null || !email.matches(emailRegex)) {
            errorMessage.append("Please enter a valid email address.<br>");
            isValid = false;
        }

        // Password validation using regex
        String passwordRegex = "^(?=.[0-9])(?=.[a-z])(?=.*[A-Z]).{8,}$";
        if (password == null || !password.matches(passwordRegex)) {
            errorMessage.append("Password must be at least 8 characters long, with uppercase, lowercase, and a number.<br>");
            isValid = false;
        }

        if (isValid) {
            // JDBC code to insert data into the database
            try {
                Class.forName("com.mysql.cj.jdbc.Driver");
                Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);

                String query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                PreparedStatement stmt = conn.prepareStatement(query);
                stmt.setString(1, username);
                stmt.setString(2, email);
                stmt.setString(3, password);

                int rowCount = stmt.executeUpdate();
                if (rowCount > 0) {
                    out.println("<div class='success-message'>Registration successful!</div>");
                } else {
                    out.println("<div class='error'>Registration failed. Please try again.</div>");
                }

                stmt.close();
                conn.close();
            } catch (Exception e) {
                e.printStackTrace();
                out.println("<div class='error'>Database error: " + e.getMessage() + "</div>");
            }
        } else {
            out.println("<div class='error'>" + errorMessage.toString() + "</div>");
        }

        out.println("<a href='register.html'>Go Back</a>");
    }
}
