import "./App.css";
import Login from "./views/Login";
import Register from "./views/Register";
import Timelines from "./views/Timelines";
import AuthLayout from "./layouts/AuthLayout";
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import "./App.css";

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/dashboard" element={<AuthLayout />}>
          <Route path="/dashboard" element={<Timelines />} />
          <Route path="/dashboard/:timelineId" element={<Timelines />} />
        </Route>
      </Routes>
    </Router>
  );
}

export default App;
