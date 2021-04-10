import { ANIMATION_DURATION_SC, GUTTER } from '@/Config/src'
import { device } from '@/Style/src/components/devices'
import { createGlobalStyle, css } from 'styled-components'
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
  ::selection {
    background: ${({ theme }) => theme['selection.bg']};
    color: ${({ theme }) => theme['selection.fg']};
  }
  html {
    font-size: 75%;
    background: ${({ theme }) => theme['html.bg']};
    scroll-behavior: smooth;
  }
  body {
    background: ${({ theme }) => theme['body.bg']};
    color: ${({ theme }) => theme['body.fg']};
    font-family: 'Noto Sans CJK SC', 'Helvetica Neue', Helvetica, Arial, Verdana,
      Geneva, sans-serif;
    padding: ${GUTTER};
    margin: 0;
    line-height: 1.5;
    /* will-change: transform; */
  }
  a {
    cursor: pointer;
    color: ${({ theme }) => theme['a.fg']};
    text-decoration: none;
    :hover,
    :active {
      color: ${({ theme }) => theme['a.fg']};
      text-decoration: underline;
    }
  }
`
export const Normalize = createGlobalStyle`${normalize}`
