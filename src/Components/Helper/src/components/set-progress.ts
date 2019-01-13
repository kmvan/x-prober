import gradientColors from '~components/Helper/src/components/gradient'

const setProgress = (progress: HTMLElement, usage: number) => {
  if (usage > 100) {
    usage = 100
  }

  const colors = gradientColors('#00cc00', '#ef2d2d')
  progress.style.backgroundColor = colors[~~usage - 1]
  progress.style.width = `${usage}%`
}

export default setProgress
