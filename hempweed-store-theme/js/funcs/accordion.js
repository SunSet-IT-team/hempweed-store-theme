export const accordion = () => {
  const accordionButtons = document.querySelectorAll('._accordion_btn');
  const accordionContents = document.querySelectorAll('._accordion_hidden_content');

  let activeBlockIndex = 1;

  // --- Model ---
  const setInitialStyles = (content) => {
    content.style.maxHeight = '0';
    content.style.overflow = 'hidden';
    content.style.transition = 'max-height 0.3s ease';
  };

  const calculateBlockHeight = (content) => {
    content.style.maxHeight = 'auto';
    const height = content.scrollHeight + 'px';
    return height;
  };

  const hideContent = (content) => {
    content.style.maxHeight = '0';
  };

  const showContent = (content, height) => {
    content.style.maxHeight = height;
  };

  // View
  const render = () => {
    accordionContents.forEach((content, index) => {
      setInitialStyles(content);

      if (index === activeBlockIndex) {
        const height = calculateBlockHeight(content);
        showContent(content, height);
      } else {
        hideContent(content);
      }
    });
  };

  // Controller
  const handleButtonClick = (index) => {
    return () => {
      activeBlockIndex = (activeBlockIndex === index) ? -1 : index;
      render();
    };
  };

  accordionButtons.forEach((button, index) => {
    button.addEventListener('click', handleButtonClick(index));
  });

  render();
};