import { useState, useEffect } from "react";
import { Layout } from "antd";
import ProfileMenu from "./ProfileMenu";
import Logo from "../../assets/logo.png";
import "./HeaderNav.css";

const { Header } = Layout;

export const HeaderNav = () => {
  const [isMobile, setIsMobile] = useState(false);
  const [isNavTop, setIsNavTop] = useState(false);
  const [navCollapsed] = useState(false);
  const [headerNavColor] = useState("rgb(255, 255, 255)");

  useEffect(() => {
    const updateWidth = () => {
      const width = window.innerWidth;
      const ismobile = width < 992;
      setIsMobile(ismobile);
    };
    window.addEventListener("resize", updateWidth);
    updateWidth();
    return () => window.removeEventListener("resize", updateWidth);
  }, []);

  useEffect(() => {
    if (isMobile) {
      document.body.classList.remove("layout-top-nav");
      setIsNavTop(false);
    }
  }, [isMobile]);

  useEffect(() => {
    if (isNavTop) {
      document.body.classList.add("layout-top-nav");
    } else {
      document.body.classList.remove("layout-top-nav");
    }
  }, [isNavTop]);

  const getNavWidth = () => {
    if (isNavTop || isMobile) {
      return "0px";
    }
    return navCollapsed ? "80px" : "256px";
  };

  return (
    <Header
      className="app-header"
      style={{
        backgroundColor: headerNavColor,
        left: `${getNavWidth()}`,
      }}
    >
      <div className="app-header-inner">
        {!isMobile && (
          <div>
            <a>
              <img
                src={Logo}
                alt="ChronoLink"
                width={50}
                height={50}
                className="app-header-logo"
              />
            </a>
          </div>
        )}
        <div className="app-header-avatar">
          <ProfileMenu />
        </div>
      </div>
    </Header>
  );
};

export default HeaderNav;
