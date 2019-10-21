import { css, createGlobalStyle } from 'styled-components'
import { COLOR_DARK, GUTTER } from '~components/Config/src'
import { device } from '~components/Style/src/components/devices'

const normalize = css`
  @media ${device('desktopSm')} {
    ::-webkit-scrollbar-track {
      background-color: transparent;
    }

    ::-webkit-scrollbar {
      width: ${GUTTER};
      background-color: transparent;
    }

    ::-webkit-scrollbar-thumb {
      border-radius: ${GUTTER} 0 0 ${GUTTER};
      background-color: #ccc;

      :hover {
        background-color: #fff;
      }
    }
  }

  * {
    box-sizing: border-box;
    word-break: break-all;
  }

  html {
    font-size: 75%;
    background: ${COLOR_DARK};
    scroll-behavior: smooth;
  }

  body {
    background: ${COLOR_DARK};
    color: ${COLOR_DARK};
    font-family: 'Noto Sans CJK SC', 'Helvetica Neue', Helvetica, Arial, Verdana,
      Geneva, sans-serif;
    padding: ${GUTTER};
    margin: 0;
    line-height: 1.5;
    /* will-change: transform; */
  }

  a {
    cursor: pointer;
    color: ${COLOR_DARK};
    text-decoration: none;

    :hover,
    :active {
      color: ${COLOR_DARK};
      text-decoration: underline;
    }
  }
`

const Normalize = createGlobalStyle`${normalize}`

export default Normalize
