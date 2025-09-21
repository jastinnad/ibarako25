import { createRoot } from "react-dom/client";
import App from "./App.tsx";
import "./index.css";
import "./assets/css/style.css"; // include custom utility/style sheet in bundle

createRoot(document.getElementById("root")!).render(<App />);
