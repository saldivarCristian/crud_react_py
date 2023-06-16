import React, { Suspense } from "react";
import styled from "styled-components";
import Navbar from "./Navbar";
import { Canvas } from "@react-three/fiber";
import { OrbitControls, Sphere, MeshDistortMaterial } from "@react-three/drei";
import { Link } from "react-router-dom";

// import FileList from './FileList';
const Section = styled.div`
  height: 100vh;
  scroll-snap-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;

  @media only screen and (max-width: 768px) {
    height: 122vh;
  }
`;

const Container = styled.div`
  height: 100%;
  scroll-snap-align: center;
  width: 1400px;
  display: flex;
  justify-content: space-between;

  @media only screen and (max-width: 768px) {
    width: 100%;
    height: 80%;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
`;

const Left = styled.div`
  flex: 2;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 20px;

  @media only screen and (max-width: 768px) {
    flex: 1;
    align-items: center;
  }
`;

const Title = styled.h1`
  font-size: 50px;

  @media only screen and (max-width: 768px) {
    text-align: center;
    font-size: 54px;
  }
`;

const WhatWeDo = styled.div`
  display: flex;
  align-items: center;
  gap: 10px;
`;

const Line = styled.img`
  height: 5px;
`;

const Subtitle = styled.h2`
  color: #da4ea2;
`;

const Desc = styled.p`
  font-size: 20px;
  color: lightgray;
  @media only screen and (max-width: 768px) {
    padding: 20px;
    text-align: center;
  }
`;

const ButtonApp = styled.button`
  background-color: #da4ea2;
  color: white;
  font-weight: 500;
  width: 200px;
  padding: 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
`;

const Right = styled.div`
  flex: 3;
  position: relative;
  @media only screen and (max-width: 768px) {
    flex: 1;
    width: 100%;
  }
`;

const Img = styled.img`
  width: 800px;
  height: 600px;
  object-fit: contain;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  margin: auto;
  animation: animate 2s infinite ease alternate;

  @media only screen and (max-width: 768px) {
    width: 300px;
    height: 300px;
  }

  @keyframes animate {
    to {
      transform: translateY(20px);
    }
  }
`;



const verificarApp = () =>{
  alert()
}

const Hero = () => {
  return (
    <Section>
      <Navbar />
      <Container>
        <Left>
          <Title>¡Descubre nuestra nueva plataforma de gestión!</Title>
          <Desc>
            La Universidad se complace en presentar la versión actualizada de
            nuestra plataforma de gestión. Hemos incorporado nuevas
            características y mejoras para facilitar tus tareas administrativas.
            ¡Experimenta la eficiencia en tus manos!
          </Desc>
          {/* <WhatWeDo>
            <Line src="./img/line.png" />
            <Subtitle> Beneficios destacados: </Subtitle>
          </WhatWeDo>
          <Desc>
            - Interfaz intuitiva y fácil de usar. - Acceso rápido a información
            actualizada en tiempo real. - Mayor eficiencia en la gestión de los
            sistemas universitarios. - Descarga nuestras herramientas de
            escritorio para una experiencia completa.
          </Desc> */}
          {/* <FileList /> */}

          <Link to="/admin">
            <ButtonApp
              onClick={() => {
                // verificarApp()
              }}
            >
              {" "}
              Accede al Admin{" "}
            </ButtonApp>
          </Link>
          {/* <Dialog open={open} onClose={handleClose}>
            <div style={{ padding: 20 }}>
              <TextField
                label="Nombre de usuario"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                margin="normal"
              />
              <TextField
                label="Contraseña"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                margin="normal"
              />
              <Button variant="contained" color="primary" onClick={handleDownload}>
                Descargar
              </Button>
            </div>
          </Dialog> */}

          {/* <Button>Descarga aqui la aplicaci</Button>
          <Button>Descarga aqui la aplicaci</Button> */}
        </Left>
        <Right>
          <Canvas>
            <Suspense fallback={null}>
              <OrbitControls enableZoom={false} />
              <ambientLight intensity={1} />
              <directionalLight position={[3, 2, 1]} />
              <Sphere args={[1, 100, 200]} scale={2.8}>
                <MeshDistortMaterial
                  color="#3d1c56"
                  attach="material"
                  distort={0.5}
                  speed={2}
                />
              </Sphere>
            </Suspense>
          </Canvas>
          <Img src="./img/logo_uninorte.png" />
        </Right>
      </Container>
    </Section>
  );
};

export default Hero;
