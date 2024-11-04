import backgroundImage from "../assets/background.jpg";
import RegisterCard from "../components/RegisterCard/RegisterCard";

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
        <RegisterCard />
      </div>
    </>
  );
};

export default Login;
