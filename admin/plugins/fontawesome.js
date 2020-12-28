import Vue from 'vue'
import {library, config} from '@fortawesome/fontawesome-svg-core'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

import {
    faOutdent,
    faHome,
    faPowerOff,
    faTachometerAlt,
    faRoute,
    faScroll,
    faUsers,
    faUserTag,
    faGlobe,
    faLanguage,
    faExchangeAlt,
    faCogs,
    faAlignJustify,
    faChevronRight,
    faPlus,
    faPencilAlt,
    faTrashAlt,
    faSave,
    faReply,
    faMap,
    faHeart,
    faShareAlt,
    faQuestionCircle,
    faPaperPlane,
    faClone,
    faPuzzlePiece
} from '@fortawesome/free-solid-svg-icons'

import {
    faListAlt,
    faNewspaper,
    faCheckCircle,
    faTimesCircle,
    faCreditCard
} from '@fortawesome/free-regular-svg-icons'

config.autoAddCss = false

library.add(
    faOutdent,
    faHome,
    faPowerOff,
    faTachometerAlt,
    faListAlt,
    faRoute,
    faNewspaper,
    faScroll,
    faUsers,
    faUserTag,
    faGlobe,
    faLanguage,
    faExchangeAlt,
    faCogs,
    faAlignJustify,
    faChevronRight,
    faPlus,
    faCheckCircle,
    faTimesCircle,
    faPencilAlt,
    faTrashAlt,
    faSave,
    faReply,
    faMap,
    faHeart,
    faCreditCard,
    faShareAlt,
    faGlobe,
    faQuestionCircle,
    faPaperPlane,
    faClone,
    faPuzzlePiece
)

Vue.component('fa', FontAwesomeIcon)
