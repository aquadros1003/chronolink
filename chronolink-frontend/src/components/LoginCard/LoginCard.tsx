import LoginForm from "./LoginForm";
import { Card, Row, Col } from "antd";
import Logo from "../../assets/logo.png";

const LoginCard = () => {
  return (
    <>
      <div className="card-container">
        <Col xs={50} sm={30} md={30} lg={12}>
          <Card>
            <div className="my-4">
              <div className="card-header text-center">
                <img
                  className="img-fluid"
                  src={Logo}
                  alt=""
                  width={158.4}
                  height={158.4}
                />
              </div>
              <Row justify="center">
                <Col xs={24} sm={30} md={30} lg={20}>
                  <LoginForm />
                </Col>
              </Row>
            </div>
          </Card>
        </Col>
      </div>
    </>
  );
};

export default LoginCard;
