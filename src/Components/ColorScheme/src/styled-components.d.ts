import { colorSchemeProps } from './typings'
declare module 'styled-components' {
  export interface DefaultTheme extends colorSchemeProps {}
}
