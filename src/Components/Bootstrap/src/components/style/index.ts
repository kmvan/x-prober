import { css, createGlobalStyle } from 'styled-components'
import { DARK_COLOR, GUTTER } from '~components/Config/src'

const normalize = css`
  * {
    box-sizing: border-box;
    word-break: break-all;
  }
  html {
    font-size: 75%;
    background: #333;
  }
  body {
    background: ${DARK_COLOR};
    color: ${DARK_COLOR};
    font-family: 'Noto Sans CJK SC', 'Helvetica Neue', Helvetica, Arial, Verdana,
      Geneva, sans-serif;
    padding: ${GUTTER};
    margin: 0;
    line-height: 1.5;
  }
  a {
    cursor: pointer;
    color: ${DARK_COLOR};
    text-decoration: none;

    &:hover,
    &:active {
      color: ${DARK_COLOR};
      text-decoration: underline;
    }
  }
`

const Normalize = createGlobalStyle`${normalize}`

export default Normalize
// @import "~components/Style/src/components/style/grid";
// @import "./emoji";
// @import "./app";
// @import "./body";
// @import "./html";
// @import "./title";
// @import "./a";
// @import "./mod";
// @import "./group";
// @import "./alert";
// @import "./progress";
// @import "./tooltip";
// @import "./break-normal";
