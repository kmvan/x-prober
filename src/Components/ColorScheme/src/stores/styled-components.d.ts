import { ColorSchemeProps } from '.'

declare module 'styled-components' {
  export interface DefaultTheme extends ColorSchemeProps {}
}
