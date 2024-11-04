import backgroundImage from "../assets/background.jpg";
import LoginCard from "../components/LoginCard/LoginCard";

const Login = () => {
  return (
    <>
      <div
        style={{
          backgroundImage: `url(${backgroundImage})`,
          backgroundSize: "cover",
          backgroundPosition: "center",
          backgroundRepeat: "no-repeat",
          height: "100vh",
          width: "100vw",
        }}
      >
        <LoginCard />
      </div>
    </>
  );
};

export default Login;
