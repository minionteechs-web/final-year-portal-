// three-portal.js – runs only when Three.js is available
(() => {
  'use strict';

  // abort if Three.js isn't loaded (e.g. JS disabled)
  if (typeof THREE === 'undefined') return;

  // configuration
  const lecturerImages = [
    '/assets/img/lecturer-01.jpg',
    '/assets/img/lecturer-02.jpg',
    '/assets/img/lecturer-03.jpg'
  ];

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(
    60,
    window.innerWidth / window.innerHeight,
    0.1,
    100
  );
  camera.position.z = 4;

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.setPixelRatio(window.devicePixelRatio);
  document.getElementById('three-container').appendChild(renderer.domElement);

  // lights
  const hemi = new THREE.HemisphereLight(0xffffff, 0x444444, 0.9);
  scene.add(hemi);
  const dir = new THREE.DirectionalLight(0xffffff, 0.8);
  dir.position.set(5, 5, 5);
  scene.add(dir);

  // orbit controls
  const controls = new THREE.OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true;

  // create a textured plane for each lecturer photo
  lecturerImages.forEach((src, i) => {
    const tex = new THREE.TextureLoader().load(src, () => {
      tex.magFilter = THREE.LinearFilter;
      tex.minFilter = THREE.LinearMipmapLinearFilter;
    });

    const geometry = new THREE.PlaneGeometry(1.2, 1.2);
    const material = new THREE.MeshStandardMaterial({
      map: tex,
      roughness: 0.5,
      metalness: 0.1
    });

    const mesh = new THREE.Mesh(geometry, material);
    const angle = (i / lecturerImages.length) * Math.PI * 2;
    const radius = 2 + Math.random() * 0.5;
    mesh.position.set(Math.cos(angle) * radius, Math.sin(angle * 0.5) * radius, -2);
    mesh.rotation.z = Math.random() * Math.PI;
    scene.add(mesh);
  });

  // animation loop
  function animate() {
    requestAnimationFrame(animate);
    scene.rotation.y += 0.005;
    scene.rotation.x += 0.002;
    controls.update();
    renderer.render(scene, camera);
  }
  animate();

  // resize handling
  window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });
})();