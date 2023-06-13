import React from "react";
import { styled } from "@mui/system";

import Contact from "../../components/inicio/Contact";
import Hero from "../../components/inicio/Hero";
import Who from "../../components/inicio/Who";
import Works from "../../components/inicio/Works";

const Container = styled("div")({
  height: "100vh",
  scrollSnapType: "y mandatory",
  scrollBehavior: "smooth",
  overflowY: "auto",
  scrollbarWidth: "none",
  color: "white",
  background: 'url("./img/bg.jpeg")',
  "&::-webkit-scrollbar": {
    display: "none",
  },
});

function Home() {
  return (
    <Container>
      <Hero/>
      <Who />
      {/* <Works /> */}
      <Contact />
    </Container>
  );
}

export default Home;
