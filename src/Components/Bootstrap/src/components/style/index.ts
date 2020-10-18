import { css, createGlobalStyle } from 'styled-components'
import { GUTTER, ANIMATION_DURATION_SC } from '@/Config/src'
import { device } from '@/Style/src/components/devices'

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
    transition: background ${ANIMATION_DURATION_SC}s;
  }

  html {
    font-size: 75%;
    background: ${({ theme }) => theme.colorDark};
    scroll-behavior: smooth;
  }

  body {
    background: ${({ theme }) => theme.colorDark};
    color: ${({ theme }) => theme.colorDark};
    font-family: 'Noto Sans CJK SC', 'Helvetica Neue', Helvetica, Arial, Verdana,
      Geneva, sans-serif;
    padding: ${GUTTER};
    margin: 0;
    line-height: 1.5;
    /* will-change: transform; */
  }

  a {
    cursor: pointer;
    color: ${({ theme }) => theme.colorDark};
    text-decoration: none;

    :hover,
    :active {
      color: ${({ theme }) => theme.colorDark};
      text-decoration: underline;
    }
  }
`

const Normalize = createGlobalStyle`${normalize}`

export default Normalize
